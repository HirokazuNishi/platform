<?php

namespace Oro\Bundle\GridBundle\Datagrid;

use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\ValidatorInterface;
use Symfony\Component\Routing\Router;

use Oro\Bundle\GridBundle\Builder\DatagridBuilderInterface;
use Oro\Bundle\GridBundle\Builder\ListBuilderInterface;
use Oro\Bundle\GridBundle\Field\FieldDescriptionInterface;
use Oro\Bundle\GridBundle\Field\FieldDescriptionCollection;
use Oro\Bundle\GridBundle\Property\PropertyInterface;
use Oro\Bundle\GridBundle\Datagrid\ParametersInterface;
use Oro\Bundle\GridBundle\Route\RouteGeneratorInterface;
use Oro\Bundle\GridBundle\Sorter\SorterInterface;

abstract class DatagridManager implements DatagridManagerInterface
{
    /**
     * @var DatagridBuilderInterface
     */
    protected $datagridBuilder;

    /**
     * @var ListBuilderInterface
     */
    protected $listBuilder;

    /**
     * @var QueryFactoryInterface
     */
    protected $queryFactory;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var ParametersInterface
     */
    protected $parameters;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $entityHint;

    /**
     * @var RouteGeneratorInterface
     */
    protected $routeGenerator;

    /**
     * @var FieldDescriptionCollection
     */
    private $fieldsCollection;

    /**
     * {@inheritDoc}
     */
    public function setDatagridBuilder(DatagridBuilderInterface $datagridBuilder)
    {
        $this->datagridBuilder = $datagridBuilder;
    }

    /**
     * {@inheritDoc}
     */
    public function setListBuilder(ListBuilderInterface $listBuilder)
    {
        $this->listBuilder = $listBuilder;
    }

    /**
     * {@inheritDoc}
     */
    public function setQueryFactory(QueryFactoryInterface $queryManager)
    {
        $this->queryFactory = $queryManager;
    }

    /**
     * {@inheritDoc}
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritDoc}
     */
    public function setValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * {@inheritDoc}
     */
    public function setRouter(Router $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritDoc}
     */
    public function setRouteGenerator(RouteGeneratorInterface $routeGenerator)
    {
        $this->routeGenerator = $routeGenerator;
    }

    /**
     * {@inheritDoc}
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritDoc}
     */
    public function setEntityHint($entityHint)
    {
        $this->entityHint = $entityHint;
    }

    /**
     * {@inheritDoc}
     */
    public function setParameters(ParametersInterface $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * {@inheritDoc}
     */
    public function getDatagrid()
    {
        // add datagrid fields
        $listCollection = $this->listBuilder->getBaseList();

        /** @var $fieldDescription FieldDescriptionInterface */
        foreach ($this->getListFields() as $fieldDescription) {
            $listCollection->add($fieldDescription);
        }

        // merge default parameters
        $parametersArray = $this->parameters->toArray();
        if (empty($parametersArray[$this->name])) {
            foreach ($this->getDefaultParameters() as $type => $value) {
                $this->parameters->set($type, $value);
            }
        }

        // create query
        $query = $this->createQuery();
        $this->applyQueryParameters($query);

        // create datagrid
        $datagrid = $this->datagridBuilder->getBaseDatagrid(
            $query,
            $listCollection,
            $this->routeGenerator,
            $this->parameters,
            $this->name,
            $this->entityHint
        );

        // add properties
        foreach ($this->getProperties() as $property) {
            $this->datagridBuilder->addProperty($datagrid, $property);
        }

        // add datagrid filters
        /** @var $fieldDescription FieldDescriptionInterface */
        foreach ($this->getFilters() as $fieldDescription) {
            $this->datagridBuilder->addFilter($datagrid, $fieldDescription);
        }

        // add datagrid sorters
        /** @var $sorterField FieldDescriptionInterface */
        foreach ($this->getSorters() as $sorterField) {
            $this->datagridBuilder->addSorter($datagrid, $sorterField);
        }

        // add row actions
        foreach ($this->getRowActions() as $actionParameters) {
            $this->datagridBuilder->addRowAction($datagrid, $actionParameters);
        }

        return $datagrid;
    }

    /**
     * @return ProxyQueryInterface
     */
    protected function createQuery()
    {
        return $this->queryFactory->createQuery();
    }

    /**
     * Apply query parameters to query object
     *
     * @param ProxyQueryInterface $query
     */
    protected function applyQueryParameters(ProxyQueryInterface $query)
    {
        foreach ($this->getQueryParameters() as $name => $value) {
            $query->setParameter($name, $value);
        }
    }

    /**
     * Get parameters for query
     *
     * @return array
     */
    protected function getQueryParameters()
    {
        return array();
    }

    protected function getFieldDescriptionCollection()
    {
        if (!$this->fieldsCollection) {
            $this->fieldsCollection = new FieldDescriptionCollection();
            $this->configureFields($this->fieldsCollection);
        }

        return $this->fieldsCollection;
    }

    /**
     * Configure collection of field descriptions
     *
     * @param FieldDescriptionCollection $fieldCollection
     */
    protected function configureFields(FieldDescriptionCollection $fieldCollection)
    {

    }

    /**
     * Get Route generator
     *
     * @return RouteGeneratorInterface
     */
    public function getRouteGenerator()
    {
        return $this->routeGenerator;
    }

    /**
     * Get list of datagrid fields
     *
     * @return FieldDescriptionInterface[]
     */
    protected function getListFields()
    {
        return $this->getFieldDescriptionCollection()->getElements();
    }

    /**
     * Get list of properties
     *
     * @return PropertyInterface
     */
    protected function getProperties()
    {
        return array();
    }

    /**
     * Get list of datagrid filters
     *
     * @return FieldDescriptionInterface[]
     */
    protected function getFilters()
    {
        $fields = array();
        /** @var $fieldDescription FieldDescriptionInterface */
        foreach ($this->getFieldDescriptionCollection() as $fieldDescription) {
            if ($fieldDescription->isFilterable()) {
                $fields[] = $fieldDescription;
            }
        }

        return $fields;
    }

    /**
     * Get list of datagrid sorters
     *
     * @return array
     */
    protected function getSorters()
    {
        $fields = array();
        /** @var $fieldDescription FieldDescriptionInterface */
        foreach ($this->getFieldDescriptionCollection() as $fieldDescription) {
            if ($fieldDescription->isSortable()) {
                $fields[] = $fieldDescription;
            }
        }

        return $fields;
    }

    /**
     * Get list of row actions
     *
     * @return array
     */
    protected function getRowActions()
    {
        return array();
    }

    /**
     * Get default parameters
     *
     * @return array
     */
    protected function getDefaultParameters()
    {
        return array(
            ParametersInterface::FILTER_PARAMETERS => $this->getDefaultFilters(),
            ParametersInterface::SORT_PARAMETERS   => $this->getDefaultSorters(),
            ParametersInterface::PAGER_PARAMETERS  => $this->getDefaultPager()
        );
    }

    /**
     * @return array
     */
    protected function getDefaultSorters()
    {
        $sorters = array();

        // get first sortable field
        $fields = $this->getSorters();
        if (!empty($fields)) {
            /** @var $field FieldDescriptionInterface */
            $field = reset($fields);
            $sorters[$field->getName()] = SorterInterface::DIRECTION_ASC;
        }

        return $sorters;
    }

    /**
     * @return array
     */
    protected function getDefaultFilters()
    {
        return array();
    }

    /**
     * @return array
     */
    protected function getDefaultPager()
    {
        return array();
    }
}
