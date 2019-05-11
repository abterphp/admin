<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Controllers\Admin\Form;

use AbterPhp\Admin\Domain\Entities\UserApiKey as Entity;
use AbterPhp\Admin\Form\Factory\UserApiKey as FormFactory;
use AbterPhp\Admin\Orm\UserApiKeyRepo as Repo;
use AbterPhp\Framework\Assets\AssetManager;
use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use AbterPhp\Framework\Http\Controllers\Admin\FormAbstract;
use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\Session\FlashService;
use Cocur\Slugify\Slugify;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Routing\Urls\UrlGenerator;
use Opulence\Sessions\ISession;

class UserApiKey extends FormAbstract
{
    const ENTITY_SINGULAR = 'userApiKey';
    const ENTITY_PLURAL   = 'userApiKeys';

    const ENTITY_TITLE_SINGULAR = 'admin:userApiKey';
    const ENTITY_TITLE_PLURAL   = 'admin:userApiKeys';

    /** @var Slugify */
    protected $slugify;

    /** @var string */
    protected $resource = 'user_api_keys';

    /**
     * ApiKey constructor.
     *
     * @param FlashService $flashService
     * @param ITranslator  $translator
     * @param UrlGenerator $urlGenerator
     * @param Repo         $repo
     * @param ISession     $session
     * @param FormFactory  $formFactory
     * @param AssetManager $assetManager
     */
    public function __construct(
        FlashService $flashService,
        ITranslator $translator,
        UrlGenerator $urlGenerator,
        Repo $repo,
        ISession $session,
        FormFactory $formFactory,
        IEventDispatcher $eventDispatcher
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
    }

    /**
     * @param string $entityId
     *
     * @return Entity
     */
    public function createEntity(string $entityId): IStringerEntity
    {
        $entity = new Entity((string)$entityId, '', '');

        return $entity;
    }
}
