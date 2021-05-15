<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Controllers\Admin;

use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use AbterPhp\Framework\Http\Controllers\ControllerAbstract;
use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\Session\FlashService;
use Opulence\Routing\Urls\UrlGenerator;
use Psr\Log\LoggerInterface;

abstract class AdminAbstract extends ControllerAbstract
{
    public const ENTITY_PLURAL   = '';
    public const ENTITY_SINGULAR = '';

    public const ENTITY_LOAD_FAILURE = 'framework:load-failure';

    public const URL_EDIT = '%s-edit';

    public const RESOURCE_DEFAULT = '%s';
    public const RESOURCE_HEADER  = '%s-header';
    public const RESOURCE_FOOTER  = '%s-footer';
    public const RESOURCE_TYPE    = 'void';

    public const LOG_CONTEXT_EXCEPTION  = 'Exception';
    public const LOG_PREVIOUS_EXCEPTION = 'Previous exception #%d';

    public const ROUTING_PATH = '';

    /** @var ITranslator */
    protected ITranslator $translator;

    /** @var UrlGenerator */
    protected $urlGenerator;

    /** @var string */
    protected string $resource = '';

    /**
     * AdminAbstract constructor.
     *
     * @param FlashService    $flashService
     * @param LoggerInterface $logger
     * @param ITranslator     $translator
     * @param UrlGenerator    $urlGenerator
     */
    public function __construct(
        FlashService $flashService,
        LoggerInterface $logger,
        ITranslator $translator,
        UrlGenerator $urlGenerator
    ) {
        parent::__construct($flashService, $logger);

        $this->translator   = $translator;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param IStringerEntity|null $entity
     */
    protected function addCustomAssets(?IStringerEntity $entity = null)
    {
        $this->prepareCustomAssets();
    }

    protected function prepareCustomAssets()
    {
        $this->view->setVar('page', $this->getResourceName(static::RESOURCE_DEFAULT));
        $this->view->setVar('pageHeader', $this->getResourceName(static::RESOURCE_HEADER));
        $this->view->setVar('pageFooter', $this->getResourceName(static::RESOURCE_FOOTER));
        $this->view->setVar('pageType', $this->getResourceTypeName(static::RESOURCE_DEFAULT));
        $this->view->setVar('pageTypeHeader', $this->getResourceTypeName(static::RESOURCE_HEADER));
        $this->view->setVar('pageTypeFooter', $this->getResourceTypeName(static::RESOURCE_FOOTER));
    }

    /**
     * @param string $template
     *
     * @return string
     */
    protected function getResourceName(string $template)
    {
        return sprintf($template, static::ENTITY_SINGULAR);
    }

    /**
     * @param string $template
     *
     * @return string
     */
    protected function getResourceTypeName(string $template)
    {
        return sprintf($template, static::RESOURCE_TYPE);
    }

    /**
     * @param \Exception $exception
     *
     * @return array
     */
    protected function getExceptionContext(\Exception $exception): array
    {
        $result = [static::LOG_CONTEXT_EXCEPTION => $exception->getMessage()];

        $i = 1;
        while ($exception = $exception->getPrevious()) {
            $result[sprintf(static::LOG_PREVIOUS_EXCEPTION, $i++)] = $exception->getMessage();
        }

        return $result;
    }
}
