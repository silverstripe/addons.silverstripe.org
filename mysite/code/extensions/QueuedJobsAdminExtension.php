<?php

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
        $gridField = $form->Fields()->fieldByName('QueuedJobDescriptor');
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
