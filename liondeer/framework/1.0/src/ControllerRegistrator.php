<?php

namespace App;

use App\Controller\ConfigFeatureController;
use App\Controller\OutgoingInvoiceSourceController;
use Liondeer\Framework\Controller\AbstractConfigFeatureController;
use Liondeer\Framework\Controller\AbstractDmsObjectExtensionController;
use Liondeer\Framework\Controller\AbstractFeatureController;
use Liondeer\Framework\Controller\AbstractSourceController;
use Liondeer\Framework\Exception\LiondeerD3FrameworkException;

class ControllerRegistrator
{
    /** @var AbstractFeatureController[] */
    private array $featureControllers = [];
    /** @var AbstractConfigFeatureController[] */
    private array $configFeatureControllers = [];
    /** @var AbstractDmsObjectExtensionController[] */
    private array $dmsObjectExtensionControllers = [];
    /** @var AbstractSourceController[] */
    private array $sourceControllers = [];

    //Im Konstruktor müssen alle Feature- und ConfigFeature-Controller registriert werden

    /**
     * @throws LiondeerD3FrameworkException
     */
    public function __construct(
        ConfigFeatureController $configFeatureController,
        OutgoingInvoiceSourceController $outgoingInvoiceSourceController,
    ){
        foreach (func_get_args() as $object) {
            if (is_a($object, AbstractFeatureController::class)) {
                array_push($this->featureControllers, $object);
            } elseif (is_a($object, AbstractConfigFeatureController::class)) {
                array_push($this->configFeatureControllers, $object);
            } elseif (is_a($object, AbstractDmsObjectExtensionController::class)) {
                array_push($this->dmsObjectExtensionControllers, $object);
            } elseif (is_a($object, AbstractSourceController::class)) {
                array_push($this->sourceControllers, $object);
            } else {
                $message = 'The class is neither "'
                    . AbstractFeatureController::class
                    . '" or "'
                    . AbstractConfigFeatureController::class
                    .'" or "'
                    .AbstractDmsObjectExtensionController::class
                    .'" or "'
                    .AbstractSourceController::class;

                throw new LiondeerD3FrameworkException($message, 'LD-1000');
            }
        }
    }

    public function getFeatureControllers(): array
    {
        return $this->featureControllers;
    }

    public function getConfigFeatureControllers(): array
    {
        return $this->configFeatureControllers;
    }

    public function getDmsObjectExtensionControllers(): array
    {
        return $this->dmsObjectExtensionControllers;
    }

    public function getSourceControllers(): array
    {
        return $this->sourceControllers;
    }
}