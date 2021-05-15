<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Controllers\Admin\Form;

use AbterPhp\Admin\Domain\Entities\User as Entity;
use AbterPhp\Admin\Domain\Entities\UserLanguage;
use AbterPhp\Admin\Form\Factory\User as FormFactory;
use AbterPhp\Admin\Http\Controllers\Admin\FormAbstract;
use AbterPhp\Admin\Orm\UserRepo as Repo;
use AbterPhp\Framework\Assets\AssetManager;
use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\Session\FlashService;
use Cocur\Slugify\Slugify;
use League\Flysystem\FilesystemException;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Routing\Urls\UrlGenerator;
use Opulence\Sessions\ISession;
use Psr\Log\LoggerInterface;

/**
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class User extends FormAbstract
{
    public const ENTITY_PLURAL   = 'users';
    public const ENTITY_SINGULAR = 'user';

    public const ENTITY_TITLE_SINGULAR = 'admin:user';
    public const ENTITY_TITLE_PLURAL   = 'admin:users';

    public const VAR_ALL_USER_GROUPS    = 'allUserGroups';
    public const VAR_ALL_USER_LANGUAGES = 'allUserLanguages';

    protected Slugify $slugify;

    protected AssetManager $assetManager;

    protected string $frontendSalt;

    protected string $resource = 'users';

    /**
     * User constructor.
     *
     * @param FlashService     $flashService
     * @param LoggerInterface  $logger
     * @param ITranslator      $translator
     * @param UrlGenerator     $urlGenerator
     * @param Repo             $repo
     * @param ISession         $session
     * @param FormFactory      $formFactory
     * @param IEventDispatcher $eventDispatcher
     * @param AssetManager     $assetManager
     * @param string           $frontendSalt
     */
    public function __construct(
        FlashService $flashService,
        LoggerInterface $logger,
        ITranslator $translator,
        UrlGenerator $urlGenerator,
        Repo $repo,
        ISession $session,
        FormFactory $formFactory,
        IEventDispatcher $eventDispatcher,
        AssetManager $assetManager,
        string $frontendSalt
    ) {
        parent::__construct(
            $flashService,
            $logger,
            $translator,
            $urlGenerator,
            $repo,
            $session,
            $formFactory,
            $eventDispatcher
        );

        $this->assetManager = $assetManager;
        $this->frontendSalt = $frontendSalt;
    }

    /**
     * @param string $entityId
     *
     * @return Entity
     */
    public function createEntity(string $entityId): IStringerEntity
    {
        $userLanguage = new UserLanguage(
            '',
            '',
            ''
        );

        return new Entity(
            $entityId,
            '',
            '',
            '',
            true,
            true,
            $userLanguage
        );
    }

    /**
     * @param IStringerEntity|null $entity
     *
     * @throws FilesystemException
     */
    protected function addCustomAssets(?IStringerEntity $entity = null)
    {
        parent::addCustomAssets($entity);

        $footer = $this->getResourceName(static::RESOURCE_FOOTER);

        $this->assetManager->addJs($footer, '/admin-assets/vendor/sha3/sha3.js');
        $this->assetManager->addJsVar($footer, 'frontendSalt', $this->frontendSalt);
        $this->assetManager->addJs($footer, '/admin-assets/vendor/zxcvbn/zxcvbn.min.js');
        $this->assetManager->addJs($footer, '/admin-assets/js/user.js');
        $this->assetManager->addJs($footer, '/admin-assets/js/required.js');
        $this->assetManager->addJs($footer, '/admin-assets/js/validation.js');
    }
}
