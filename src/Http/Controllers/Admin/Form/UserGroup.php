<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Controllers\Admin\Form;

use AbterPhp\Admin\Domain\Entities\UserGroup as Entity;
use AbterPhp\Admin\Form\Factory\UserGroup as FormFactory;
use AbterPhp\Admin\Http\Controllers\Admin\FormAbstract;
use AbterPhp\Admin\Orm\UserGroupRepo as Repo;
use AbterPhp\Framework\Assets\AssetManager;
use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\Session\FlashService;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Routing\Urls\UrlGenerator;
use Opulence\Sessions\ISession;
use Psr\Log\LoggerInterface;

class UserGroup extends FormAbstract
{
    const ENTITY_PLURAL   = 'userGroups';
    const ENTITY_SINGULAR = 'userGroup';

    const ENTITY_TITLE_SINGULAR = 'admin:userGroup';
    const ENTITY_TITLE_PLURAL   = 'admin:userGroups';

    const ROUTING_PATH = 'user-groups';

    /** @var AssetManager */
    protected $assetManager;

    /** @var string */
    protected $resource = 'user_groups';

    /**
     * UserGroup constructor.
     *
     * @param FlashService     $flashService
     * @param ITranslator      $translator
     * @param UrlGenerator     $urlGenerator
     * @param LoggerInterface  $logger
     * @param Repo             $repo
     * @param ISession         $session
     * @param FormFactory      $formFactory
     * @param IEventDispatcher $eventDispatcher
     * @param AssetManager     $assetManager
     */
    public function __construct(
        FlashService $flashService,
        ITranslator $translator,
        UrlGenerator $urlGenerator,
        LoggerInterface $logger,
        Repo $repo,
        ISession $session,
        FormFactory $formFactory,
        IEventDispatcher $eventDispatcher,
        AssetManager $assetManager
    ) {
        parent::__construct(
            $flashService,
            $translator,
            $urlGenerator,
            $logger,
            $repo,
            $session,
            $formFactory,
            $eventDispatcher
        );

        $this->assetManager = $assetManager;
    }

    /**
     * @param string $entityId
     *
     * @return Entity
     */
    protected function createEntity(string $entityId): IStringerEntity
    {
        return new Entity($entityId, '', '');
    }

    /**
     * @param IStringerEntity|null $entity
     *
     * @throws \League\Flysystem\FileNotFoundException
     */
    protected function addCustomAssets(?IStringerEntity $entity = null)
    {
        parent::addCustomAssets($entity);

        if (!($entity instanceof Entity)) {
            return;
        }

        $footer = $this->getResourceName(static::RESOURCE_FOOTER);
        $this->assetManager->addJs($footer, '/admin-assets/js/semi-auto.js');
    }
}
