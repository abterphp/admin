<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Form\Factory;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Form\Element\Input;
use AbterPhp\Framework\Form\Extra\DefaultButtons;
use AbterPhp\Framework\Form\Form;
use AbterPhp\Framework\Html\Attribute;
use AbterPhp\Framework\Html\Helper\Attributes;
use AbterPhp\Framework\I18n\ITranslator;
use Opulence\Framework\Http\CsrfTokenChecker;
use Opulence\Http\Requests\RequestMethods;
use Opulence\Sessions\ISession;

abstract class Base implements IFormFactory
{
    protected const MULTISELECT_MIN_SIZE = 3;
    protected const MULTISELECT_MAX_SIZE = 20;

    protected ISession $session;

    protected ITranslator $translator;

    protected ?Form $form;

    /**
     * Base constructor.
     *
     * @param ISession    $session
     * @param ITranslator $translator
     */
    public function __construct(ISession $session, ITranslator $translator)
    {
        $this->session    = $session;
        $this->translator = $translator;
    }

    /**
     * @param string                  $action
     * @param string                  $method
     * @param bool                    $isMultipart
     * @param string[]                $intents
     * @param array<string,Attribute> $attributes
     *
     * @return $this
     */
    protected function createForm(
        string $action,
        string $method,
        bool $isMultipart = false,
        array $intents = [],
        array $attributes = []
    ): Base {
        $attributes ??= [];
        if ($isMultipart) {
            $attributes = Attributes::addItem($attributes, Html5::ATTR_ENCTYPE, Form::ENCTYPE_MULTIPART);
        }

        $formMethod = $method == RequestMethods::GET ? $method : RequestMethods::POST;

        $this->form = new Form($action, $formMethod, $intents, $attributes);

        $this->addHttpMethod($method);

        return $this;
    }

    /**
     * @param string $method
     */
    private function addHttpMethod(string $method)
    {
        $formAttributes = Attributes::fromArray([Html5::ATTR_TYPE => Input::TYPE_HIDDEN]);
        $this->form[]   = new Input('', Input::NAME_HTTP_METHOD, $method, [], $formAttributes);
    }

    /**
     * @return $this
     */
    protected function addDefaultElements(): Base
    {
        $name  = CsrfTokenChecker::TOKEN_INPUT_NAME;
        $value = (string)$this->session->get($name);

        $formAttributes = Attributes::fromArray([Html5::ATTR_TYPE => Input::TYPE_HIDDEN]);
        $this->form[]   = new Input($name, $name, $value, [], $formAttributes);

        return $this;
    }

    /**
     * @param string $showUrl
     *
     * @return Base
     */
    protected function addDefaultButtons(string $showUrl): Base
    {
        $buttons = new DefaultButtons();

        $buttons
            ->addSaveAndBack()
            ->addBackToGrid($showUrl)
            ->addSaveAndEdit()
            ->addSaveAndCreate();

        $this->form[] = $buttons;

        return $this;
    }

    /**
     * @param int $optionCount
     * @param int $minSize
     * @param int $maxSize
     *
     * @return int
     */
    protected function getMultiSelectSize(int $optionCount, int $minSize, int $maxSize): int
    {
        return (int)max(min($optionCount, $maxSize), $minSize);
    }
}
