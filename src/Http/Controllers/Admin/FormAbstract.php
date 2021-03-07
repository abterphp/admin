<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Controllers\Admin;

use AbterPhp\Admin\Form\Factory\IFormFactory;
use AbterPhp\Framework\Constant\Event;
use AbterPhp\Framework\Constant\Session;
use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use AbterPhp\Framework\Events\FormReady;
use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\Orm\IGridRepo;
use AbterPhp\Framework\Session\FlashService;
use Casbin\Exceptions\CasbinException;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Http\Requests\RequestMethods;
use Opulence\Http\Responses\Response;
use Opulence\Orm\OrmException;
use Opulence\Routing\Urls\URLException;
use Opulence\Routing\Urls\UrlGenerator;
use Opulence\Sessions\ISession;
use Psr\Log\LoggerInterface;

abstract class FormAbstract extends AdminAbstract
{
    const LOG_MSG_LOAD_FAILURE = 'Loading %1$s failed.';

    const ENTITY_TITLE_SINGULAR = '';

    const VIEW_FORM = 'contents/backend/form';

    const VAR_ENTITY = 'entity';
    const VAR_FORM   = 'form';

    const TITLE_NEW  = 'framework:titleNew';
    const TITLE_EDIT = 'framework:titleEdit';

    const URL_NEW = '%s-new';

    const RESOURCE_DEFAULT = '%s-form';
    const RESOURCE_HEADER  = '%s-header-form';
    const RESOURCE_FOOTER  = '%s-footer-form';
    const RESOURCE_TYPE    = 'form';

    /** @var IGridRepo */
    protected $repo;

    /** @var ISession */
    protected $session;

    /** @var IFormFactory */
    protected $formFactory;

    /** @var IEventDispatcher */
    protected $eventDispatcher;

    /**
     * FormAbstract constructor.
     *
     * @param FlashService     $flashService
     * @param LoggerInterface  $logger
     * @param ITranslator      $translator
     * @param UrlGenerator     $urlGenerator
     * @param IGridRepo        $repo
     * @param ISession         $session
     * @param IFormFactory     $formFactory
     * @param IEventDispatcher $eventDispatcher
     */
    public function __construct(
        FlashService $flashService,
        LoggerInterface $logger,
        ITranslator $translator,
        UrlGenerator $urlGenerator,
        IGridRepo $repo,
        ISession $session,
        IFormFactory $formFactory,
        IEventDispatcher $eventDispatcher
    ) {
        parent::__construct($flashService, $logger, $translator, $urlGenerator);

        $this->repo            = $repo;
        $this->session         = $session;
        $this->formFactory     = $formFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return Response
     * @throws CasbinException
     * @throws URLException
     * @throws \Throwable
     */
    public function new(): Response
    {
        $entity = $this->createEntity('');

        $url   = $this->urlGenerator->createFromName(sprintf(static::URL_NEW, static::ENTITY_PLURAL));
        $title = $this->translator->translate(static::TITLE_NEW, static::ENTITY_TITLE_SINGULAR);
        $form  = $this->formFactory->create($url, RequestMethods::POST, $this->getShowUrl(), $entity);

        $form->setTranslator($this->translator);

        $this->eventDispatcher->dispatch(Event::FORM_READY, new FormReady($form));

        $this->view = $this->viewFactory->createView(static::VIEW_FORM);
        $this->view->setVar(static::VAR_ENTITY, $entity);
        $this->view->setVar(static::VAR_FORM, $form);

        $this->addCustomAssets($entity);

        return $this->createResponse($title);
    }

    /**
     * @param string $entityId
     *
     * @return Response
     * @throws CasbinException
     * @throws URLException
     * @throws \Throwable
     */
    public function edit(string $entityId): Response
    {
        $entity = $this->retrieveEntity($entityId);

        $url   = $this->getEditUrl($entityId);
        $title = $this->translator->translate(static::TITLE_EDIT, static::ENTITY_TITLE_SINGULAR, (string)$entity);
        $form  = $this->formFactory->create($url, RequestMethods::PUT, $this->getShowUrl(), $entity);

        $this->eventDispatcher->dispatch(Event::FORM_READY, new FormReady($form));

        $form->setTranslator($this->translator);

        $this->view = $this->viewFactory->createView(sprintf(static::VIEW_FORM, strtolower(static::ENTITY_SINGULAR)));
        $this->view->setVar(static::VAR_ENTITY, $entity);
        $this->view->setVar(static::VAR_FORM, $form);

        $this->addCustomAssets($entity);

        return $this->createResponse($title);
    }

    /**
     * @param string $entityId
     *
     * @return IStringerEntity
     */
    public function retrieveEntity(string $entityId): IStringerEntity
    {
        /** @var FlashService $flashService */
        $flashService = $this->flashService;

        try {
            /** @var IStringerEntity $entity */
            $entity = $this->repo->getById($entityId);
        } catch (OrmException $e) {
            $errorMessage = $this->getMessage(static::ENTITY_LOAD_FAILURE);

            $flashService->mergeErrorMessages([$errorMessage]);

            $this->logger->info(
                sprintf(static::LOG_MSG_LOAD_FAILURE, static::ENTITY_SINGULAR),
                $this->getExceptionContext($e)
            );

            return $this->createEntity('');
        }

        return $entity;
    }

    /**
     * @param string $entityEntityId
     *
     * @return IStringerEntity
     */
    abstract protected function createEntity(string $entityEntityId): IStringerEntity;

    /**
     * @return string
     * @throws URLException
     */
    protected function getShowUrl(): string
    {
        if ($this->session->has(Session::LAST_GRID_URL)) {
            return (string)$this->session->get(Session::LAST_GRID_URL);
        }

        $url = $this->urlGenerator->createFromName(static::ROUTING_PATH);

        return $url;
    }

    /**
     * @param string $id
     *
     * @return string
     * @throws URLException
     */
    protected function getEditUrl(string $id): string
    {
        $routeName = sprintf(static::URL_EDIT, static::ROUTING_PATH);

        $url = $this->urlGenerator->createFromName($routeName, $id);

        return $url;
    }

    /**
     * @param string $messageType
     *
     * @return string
     */
    protected function getMessage(string $messageType)
    {
        $entityName = $this->translator->translate(static::ENTITY_TITLE_SINGULAR);

        return $this->translator->translate($messageType, $entityName);
    }
}
