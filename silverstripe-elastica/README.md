# Heyday's SilverStripe Elastica Module

Facilitates searching and indexing of SilverStripe CMS using ElasticSearch. We use Elastica to do all the heavy lifting in terms of communication with the elastic search server. 

This module makes it easy to use ElasticSearch with SilverStripe without limiting any of the functionality found in Elastica. Basically anything that can be done with Elastica alone can be done in conjunction with this module.

This module is a fork of [SilverStripe's Elastica Module](https://github.com/silverstripe-australia/silverstripe-elastica). 

## Features

* Uses [Elastica](https://github.com/ruflin/Elastica) to communicate with the ElasticSearch Server
* Uses [PSR/Log](https://github.com/php-fig/log) interface for logging purposes (optional)
* Uses YAML configuration to index Data Objects and Pages
* Can handle has_many, many_many, and has_one relationships in the indexed ElasticSearch document
* Can handle invalidation and reindexing of related data objects
* Can handle custom fields that are not in the database but only exist as part of an object instance
* Infers ElasticSearch document field type from the database field type defined in the corresponding SilverStripe model

## Compatibility

This release is compatible with all elasticsearch 5.x releases.
This release requires SilverStripe 4.x

If you need to work with an earlier version of elasticsearch (2.x) and SS (3.x), please try the 1.0 release of this module

## Installation

```bash
$ composer require heyday/silverstripe-elastica:~2.0
```

## Usage

### Elastica Service configuration example:
mysite/_config/search.yml
```yaml
Heyday\Elastica\ElasticaService: # Example of customising the index config on the elastic search server (completely optional).
  index_config:  
    analysis:
      analyzer:
        default :
          type : custom
          tokenizer : standard
          filter :
            - standard
            - lowercase
            - stemming_filter
      filter:
        stemming_filter:
          type: snowball
          language: English

---
Only:
  environment: dev
---
SilverStripe\Core\Injector\Injector:
  Elastica\Client:
    constructor:
      - host: localhost # hostname of the elastic search server
        port: 9200 # port number of the elastic search server

  Heyday\Elastica\ElasticaService:
    constructor:
      - %$Elastica\Client
      - name-of-index  # name of the index on the elastic search server
      - %$Logger  # your error logger (must implement psr/log interface)
      - 64MB      # increases memory limit while indexing 

```

### Index configuration example:
mysite/_config/search.yml
```yaml

# PageTypes

Your\Namespace\Page:
  extensions:
    - Heyday\Elastica\Searchable
  indexed_fields: &page_defaults
    - Title
    - MenuTitle
    - Content
    - MetaDescription
    
Your\Namespace\SpecialPageWithAdditionalFields:
  extensions:
    - Heyday\Elastica\Searchable # only needed if this page does not extend the 'Page' configured above
  indexed_fields:
    <<: *page_defaults
    - BannerHeading
    - BannerCopy
    - SubHeading
    
Your\Namespace\SpecialPageWithRelatedDataObject:
  extensions:
    - Heyday\Elastica\Searchable
  indexed_fields:
    <<: *page_defaults
    - RelatedDataObjects
    
Your\Namespace\RelatedDataObject:
  extensions:
    - Heyday\Elastica\Searchable
  indexed_fields:
    - Title
    - SomeOtherField
  dependent_classes:
    - SpecialPageWithRelatedDataObject # invalidates the index for SpecialPageWithRelatedDataObject when a RelatedDataObject is updated/created

```

### Custom field index configuration example:
mysite/_config/search.yml
```yaml

# PageTypes

Your\Namespace\Page:
  extensions:
    - Heyday\Elastica\Searchable
  indexed_fields: 
    - Title
    SomeCustomFieldSimple:
      type: string
    SomeCustomFieldComplicatedConfig:
      type: string
      index_anayser: nGram_analyser
      search_analyser: whitespace_analyser
      stored: true

```

mysite/code/PageTypes/Page.php
```php
<?php

class Page extends SiteTree
{
    public function getSomeCustomFieldSimple()
    {
        return 'some dynamic text or something';
    }
    
    public function getSomeCustomFieldComplicatedConfig()
    {
        return 'the config does not have anyting to do with me';
    }
}
```

### Simple search controller configuration/implementation example:
mysite/_config/search.yml
```yaml
  SearchController:
    properties:
      SearchService: %$Heyday\Elastica\ElasticaService
```

mysite/code/Controllers/SearchController.php
```php
<?php

class SearchController extends Page_Controller
{
    /**
     * @var array
     */
    private static $allowed_actions = [
        'index'
    ];

    /**
     * @var \Heyday\Elastica\ElasticaService
     */
    protected $searchService;

    /**
     * Search results page action
     *
     * @return HTMLText
     */
    public function index()
    {
        return $this->renderWith(['SearchResults', 'Page']);
    }

    /**
     * @param \Heyday\Elastica\ElasticaService $searchService
     */
    public function setSearchService(\Heyday\Elastica\ElasticaService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * @return bool|\Heyday\Elastica\PaginatedList
     */
    public function Results()
    {
        $request = $this->getRequest();

        if ($string = $request->requestVar('for')) {

            $query = new \Elastica\Query\BoolQuery();

            $query->addMust(
                new \Elastica\Query\QueryString(strval($string))
            );

            $query->addMustNot([
                new \Elastica\Query\Type('DataObjectThatShouldNotShowUpWithResults'),
                new \Elastica\Query\Type('APageTypeThatShouldNotShowUpWithResults')
            ]);

            $results = $this->searchService->search($query);

            return new \Heyday\Elastica\PaginatedList($results, $request);
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function SearchString()
    {
        return Convert::raw2xml($this->getRequest()->requestVar('for'));
    }
}
```

## Using Queues
You can make use of queues to have your reindex processes run in the background.

We use silverstripe-queuedjobs (https://github.com/symbiote/silverstripe-queuedjobs) and a job to reindex on publish has been created.

To turn on queues, you will need the following config:
```yaml
SilverStripe\Core\Injector\Injector:
  Heyday\Elastica\Searchable:
    properties:
      Queued: true
```

You will also need to set up a cronjob (I know not very queue-like...):

Every minute to run the jobs in the queue
```
*/1 * * * * php /path/to/silverstripe/framework/cli-script.php dev/tasks/ProcessJobQueueTask
```
and to clean up the jobs, add the cleanup job once by running (it then gets automatically added to run once a day):
```
framework/sake dev/tasks/CreateQueuedJobTask?name=Symbiote\QueuedJobs\Jobs\CleanupJob
```

## License

Heyday's SilverStripe Elastica is released under the [MIT license](http://heyday.mit-license.org/)
