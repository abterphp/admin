<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Controllers\Admin;

use AbterPhp\Framework\Constant\Session;
use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use AbterPhp\Framework\Form\Extra\DefaultButtons;
use AbterPhp\Framework\Http\Service\Execute\IRepoService;
use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\Session\FlashService;
use Opulence\Http\Responses\RedirectResponse;
use Opulence\Http\Responses\Response;
use Opulence\Orm\OrmException;
use Opulence\Routing\Urls\URLException;
use Opulence\Routing\Urls\UrlGenerator;
use Opulence\Sessions\ISession;
use Psr\Log\LoggerInterface;

abstract class ExecuteAbstract extends AdminAbstract
{
    public const INPUT_NEXT = 'next';

    public const URL_CREATE = '%s-create';

    public const CREATE_SUCCESS = 'framework:create-success';
    public const CREATE_FAILURE = 'framework:create-failure';
    public const UPDATE_SUCCESS = 'framework:update-success';
    public const UPDATE_FAILURE = 'framework:update-failure';
    public const DELETE_SUCCESS = 'framework:delete-success';
    public const DELETE_FAILURE = 'framework:delete-failure';

    public const LOG_MSG_CREATE_FAILURE = 'Creating %1$s failed.';
    public const LOG_MSG_CREATE_SUCCESS = 'Creating %1$s was successful.';
    public const LOG_MSG_UPDATE_FAILURE = 'Updating %1$s with id "%2$s" failed.';
    public const LOG_MSG_UPDATE_SUCCESS = 'Updating %1$s with id "%2$s" was successful.';
    public const LOG_MSG_DELETE_FAILURE = 'Deleting %1$s with id "%2$s" failed.';
    public const LOG_MSG_DELETE_SUCCESS = 'Deleting %1$s with id "%2$s" was successful.';

    public const ENTITY_TITLE_SINGULAR = '';
    public const ENTITY_TITLE_PLURAL   = '';

    protected IRepoService $repoService;

    protected ISession $session;

    /**
     * ExecuteAbstract constructor.
     *
     * @param FlashService    $flashService
     * @param LoggerInterface $logger
     * @param ITranslator     $translator
     * @param UrlGenerator    $urlGenerator
     * @param IRepoService    $repoService
     * @param ISession        $session
     */
    public function __construct(
        FlashService $flashService,
        LoggerInterface $logger,
        ITranslator $translator,
        UrlGenerator $urlGenerator,
        IRepoService $repoService,
        ISession $session
    ) {
        parent::__construct($flashService, $logger, $translator, $urlGenerator);

        $this->repoService = $repoService;
        $this->session     = $session;
    }

    /**
     * @return Response
     * @throws URLException
     */
    public function create(): Response
    {
        $postData = $this->getPostData();
        $fileData = $this->getFileData();

        $errors = $this->repoService->validateForm(array_merge($postData, $fileData), IRepoService::CREATE);

        if (count($errors) > 0) {
            $this->flashService->mergeErrorMessages($errors);
            $this->logger->info(sprintf(static::LOG_MSG_CREATE_FAILURE, static::ENTITY_SINGULAR), $errors);

            return $this->redirectToNext();
        }

        try {
            $entity = $this->repoService->create($postData, $fileData);

            $this->logger->info(sprintf(static::LOG_MSG_CREATE_SUCCESS, static::ENTITY_SINGULAR));
            $this->flashService->mergeSuccessMessages([$this->getMessage(static::CREATE_SUCCESS)]);
        } catch (\Exception $e) {
            $this->flashService->mergeErrorMessages([$this->getMessage(static::CREATE_FAILURE)]);
            $this->logger->info(
                sprintf(static::LOG_MSG_CREATE_FAILURE, static::ENTITY_SINGULAR),
                $this->getExceptionContext($e)
            );

            return $this->redirectToNext();
        }

        return $this->redirectToNext($entity);
    }

    /**
     * @param string $entityId
     *
     * @return Response
     * @throws URLException
     */
    public function update(string $entityId): Response
    {
        $postData = $this->getPostData();
        $fileData = $this->getFileData();

        $errors = $this->repoService->validateForm(array_merge($postData, $fileData), IRepoService::UPDATE);

        try {
            $entity = $this->repoService->retrieveEntity($entityId);
        } catch (OrmException $e) {
            return $this->redirectToNext();
        }

        if (count($errors) > 0) {
            $this->logger->info(sprintf(static::LOG_MSG_UPDATE_FAILURE, static::ENTITY_SINGULAR, $entityId), $errors);
            $this->flashService->mergeErrorMessages($errors);

            return $this->redirectToNext($entity);
        }

        try {
            $this->repoService->update($entity, $postData, $fileData);
            $this->logger->info(sprintf(static::LOG_MSG_UPDATE_SUCCESS, static::ENTITY_SINGULAR, $entityId));
            $this->flashService->mergeSuccessMessages([$this->getMessage(static::UPDATE_SUCCESS)]);
        } catch (\Exception $e) {
            $this->logger->error(
                sprintf(static::LOG_MSG_UPDATE_FAILURE, static::ENTITY_SINGULAR, $entityId),
                $this->getExceptionContext($e)
            );
            $this->flashService->mergeErrorMessages([$this->getMessage(static::UPDATE_FAILURE)]);
        }

        return $this->redirectToNext($entity);
    }

    /**
     * @param string $entityId
     *
     * @return Response
     * @throws URLException
     */
    public function delete(string $entityId): Response
    {
        $entity = $this->repoService->retrieveEntity($entityId);

        try {
            $this->repoService->delete($entity);
            $this->logger->info(sprintf(static::LOG_MSG_DELETE_SUCCESS, static::ENTITY_SINGULAR, $entityId));
            $this->flashService->mergeSuccessMessages([$this->getMessage(static::DELETE_SUCCESS)]);
        } catch (\Exception $e) {
            $this->logger->error(
                sprintf(static::LOG_MSG_DELETE_FAILURE, static::ENTITY_SINGULAR, $entityId),
                [static::LOG_CONTEXT_EXCEPTION => $e->getMessage()]
            );
            $this->flashService->mergeErrorMessages([$this->getMessage(static::DELETE_FAILURE)]);
        }

        return $this->redirectToNext();
    }

    /**
     * @return array
     */
    protected function getPostData(): array
    {
        return $this->request->getPost()->getAll();
    }

    /**
     * @return array
     */
    protected function getFileData(): array
    {
        return $this->request->getFiles()->getAll();
    }

    /**
     * @param IStringerEntity|null $entity
     *
     * @return Response
     * @throws URLException
     */
    protected function redirectToNext(?IStringerEntity $entity = null): Response
    {
        $next = $this->request->getInput(static::INPUT_NEXT, DefaultButtons::BTN_VALUE_NEXT_BACK);

        $entityId = $entity ? $entity->getId() : null;

        $url = $this->getUrl($next, $entityId);

        $response = new RedirectResponse($url);
        $response->send();

        return $response;
    }

    /**
     * @param string      $next
     * @param string|null $entityId
     *
     * @return string
     * @throws URLException
     */
    protected function getUrl(string $next, string $entityId = null): string
    {
        switch ($next) {
            case DefaultButtons::BTN_VALUE_NEXT_BACK:
                return $this->getShowUrl();
            case DefaultButtons::BTN_VALUE_NEXT_EDIT:
                if (null === $entityId) {
                    return $this->getCreateUrl();
                }

                return $this->getEditUrl($entityId);
            case DefaultButtons::BTN_VALUE_NEXT_CREATE:
                return $this->getCreateUrl();
        }

        return $this->getCreateUrl();
    }

    /**
     * @return string
     * @throws URLException
     */
    protected function getCreateUrl(): string
    {
        $urlName = strtolower(sprintf(static::URL_CREATE, static::ROUTING_PATH));
        $url     = $this->urlGenerator->createFromName($urlName);

        return $url;
    }

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
