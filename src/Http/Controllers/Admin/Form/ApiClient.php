<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Controllers\Admin\Form;

use AbterPhp\Admin\Domain\Entities\ApiClient as Entity;
use AbterPhp\Admin\Form\Factory\ApiClient as FormFactory;
use AbterPhp\Admin\Http\Controllers\Admin\FormAbstract;
use AbterPhp\Admin\Orm\ApiClientRepo as Repo;
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
class ApiClient extends FormAbstract
{
    public const ENTITY_SINGULAR = 'apiClient';
    public const ENTITY_PLURAL   = 'apiClients';

    public const ENTITY_TITLE_SINGULAR = 'admin:apiClient';
    public const ENTITY_TITLE_PLURAL   = 'admin:apiClients';

    public const ROUTING_PATH = 'api-clients';

    protected Slugify $slugify;

    protected AssetManager $assets;

    protected string $secretLength;

    protected string $resource = 'api_clients';

    /**
     * ApiClient constructor.
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
     * @param string           $secretLength
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
        string $secretLength
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

        $this->assets       = $assetManager;
        $this->secretLength = $secretLength;
    }

    /**
     * @param string $entityId
     *
     * @return Entity
     */
    public function createEntity(string $entityId): IStringerEntity
    {
        return new Entity((string)$entityId, '', '', '');
    }

    /**
     * @param IStringerEntity|null $entity
     *
     * @throws FilesystemException
     */
    protected function addCustomAssets(?IStringerEntity $entity = null)
    {
        parent::addCustomAssets($entity);

        $footerResource = $this->getResourceName(static::RESOURCE_FOOTER);

        $this->assets->addJs(
            $footerResource,
            '/admin-assets/vendor/password-generator/password-generator.js'
        );
        $this->assets->addJsVar($footerResource, 'secretLength', $this->secretLength);
        $this->assets->addJs($footerResource, '/admin-assets/js/api-client.js');
    }
}
