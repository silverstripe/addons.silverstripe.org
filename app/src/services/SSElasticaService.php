<?php


use Heyday\Elastica\ElasticaService;

/**
 * Temporary class workaround for https://github.com/heyday/silverstripe-elastica/issues/19
 */
class SSElasticaService extends ElasticaService
{
    /**
     * Clears an existing index before allowing it to be recreated again
     *
     * See https://github.com/silverstripe/addons.silverstripe.org/issues/241
     */
    public function define()
    {
        $index = $this->getIndex();
        if ($index->exists()) {
            $index->delete();
        }
        parent::define();
    }
}
