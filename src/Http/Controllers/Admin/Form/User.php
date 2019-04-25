<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Controllers\Admin\Form;

use AbterPhp\Admin\Domain\Entities\User as Entity;
use AbterPhp\Admin\Domain\Entities\UserLanguage;
use AbterPhp\Admin\Form\Factory\User as FormFactory;
use AbterPhp\Admin\Orm\UserGroupRepo;
use AbterPhp\Admin\Orm\UserLanguageRepo;
use AbterPhp\Admin\Orm\UserRepo as Repo;
use AbterPhp\Framework\Assets\AssetManager;
use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use AbterPhp\Framework\Http\Controllers\Admin\FormAbstract;
use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\Session\FlashService;
use Cocur\Slugify\Slugify;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Orm\OrmException;
use Opulence\Routing\Urls\UrlGenerator;
use Opulence\Sessions\ISession;

class User extends FormAbstract
{
    const ENTITY_PLURAL   = 'users';
    const ENTITY_SINGULAR = 'user';

    const ENTITY_TITLE_SINGULAR = 'admin:user';
    const ENTITY_TITLE_PLURAL   = 'admin:users';

    const VAR_ALL_USER_GROUPS    = 'allUserGroups';
    const VAR_ALL_USER_LANGUAGES = 'allUserLanguages';

    /** @var UserGroupRepo */
    protected $userGroupRepo;

    /** @var UserLanguageRepo */
    protected $userLanguageRepo;

    /** @var Slugify */
    protected $slugify;

    /** @var AssetManager */
    protected $assets;

    /** @var string */
    protected $frontendSalt;

    /** @var string */
    protected $resource = 'users';

    /**
     * User constructor.
     *
     * @param FlashService     $flashService
     * @param ITranslator      $translator
     * @param UrlGenerator     $urlGenerator
     * @param Repo             $repo
     * @param ISession         $session
     * @param FormFactory      $formFactory
     * @param AssetManager     $assetManager
     * @param IEventDispatcher $eventDispatcher
     * @param string           $frontendSalt
     */
    public function __construct(
        FlashService $flashService,
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
            $translator,
            $urlGenerator,
            $repo,
            $session,
            $formFactory,
            $eventDispatcher
        );

        $this->assets       = $assetManager;
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
        $entity       = new Entity(
            (string)$entityId,
            '',
            '',
            '',
            true,
            true,
            $userLanguage
        );

        return $entity;
    }

    /**
     * @param Entity|null $entity
     *
     * @throws OrmException
     */
    protected function addCustomAssets(?IStringerEntity $entity = null)
    {
        parent::addCustomAssets($entity);

        $footerResource = $this->getResourceName(static::RESOURCE_FOOTER);

        $this->assets->addJs(
            $footerResource,
            '/admin-assets/vendor/sha3/sha3.min.js'
        );
        $this->assets->addJsContent(
            $footerResource,
            "var frontendSalt = '{$this->frontendSalt}'"
        );
        $this->assets->addJs(
            $footerResource,
            '/admin-assets/vendor/zxcvbn/zxcvbn.min.js'
        );
        $this->assets->addJs(
            $footerResource,
            '/admin-assets/js/user.js'
        );
    }
}
