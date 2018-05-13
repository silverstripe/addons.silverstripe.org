<?php

use SilverStripe\Forms\Form;
use Symbiote\QueuedJobs\DataObjects\QueuedJobDescriptor;
use SilverStripe\Forms\GridField\GridFieldPaginator;
use SilverStripe\Core\Extension;

class QueuedJobsAdminExtension extends Extension
{
    /**
     * Add a configurable paginator
     *
     * @param Form $form
     */
    public function updateEditForm(Form $form)
    {
        /** @var GridField $gridField */
        $gridField = $form->Fields()->fieldByName(QueuedJobDescriptor::class);
        if (!$gridField) {
            return;
        }

        $gridField->getConfig()
            ->removeComponentsByType(GridFieldPaginator::class)
            ->addComponent(
                new GridFieldConfigurablePaginator(50, [50, 100, 200, 500])
            );
    }
}
