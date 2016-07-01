<?php
/**
 * Copyright (c) 2016 Axel Helmert
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author    Axel Helmert
 * @copyright Copyright (c) 2016 Axel Helmert
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License
 */

namespace Rampage\Nexus\MongoDB\PersistenceBuilder;

use Rampage\Nexus\MongoDB\UnitOfWork;
use Rampage\Nexus\MongoDB\Driver;
use Zend\Hydrator\HydratorInterface;
use Rampage\Nexus\MongoDB\EntityState;
use Rampage\Nexus\MongoDB\InvokableChain;

/**
 * The default persistence builder
 */
class DefaultPersistenceBuilder implements PersistenceBuilderInterface
{
    const AGGREGATE_OBJECT = 1;
    const AGGREGATE_COLLECTION = 2;
    const AGGREGATE_INDEXED = 3;

    /**
     * @var UnitOfWork
     */
    protected $unitOfWork;

    /**
     * @var HydratorInterface
     */
    protected $hydrator;

    /**
     * @var Driver\CollectionInterface
     */
    protected $collection;

    /**
     * @var AggregateBuilderInterface[]
     */
    protected $aggregationProperties = [];

    /**
     * @var array
     */
    protected $mappedRefProperties = [];

    /**
     * @var array
     */
    protected $discriminatorMap = [];

    /**
     * @var string
     */
    protected $discriminatorField = null;

    /**
     * @param UnitOfWork $unitOfWork
     * @param HydratorInterface $hydrator
     */
    public function __construct(UnitOfWork $unitOfWork, HydratorInterface $hydrator, Driver\CollectionInterface $collection)
    {
        $this->hydrator = $hydrator;
        $this->unitOfWork = $unitOfWork;
        $this->collection = $collection;
    }

    /**
     * @return mixed
     */
    protected function createIdentity()
    {
        return $this->collection->createIdValue();
    }

    /**
     * Define a property as aggregate
     *
     * @param string $property
     * @param PersistenceBuilderInterface $persistenceBuilder
     *
     * @return self
     */
    public function setAggregatedProperty($property, AggregateBuilderInterface $persistenceBuilder)
    {
        $this->aggregationProperties[$property] = $persistenceBuilder;
        return $this;
    }

    /**
     * Define a non-owning side property
     *
     * This property will not be persisted.
     *
     * @param string $property
     * @return self
     */
    public function addMappedRefProperty($property)
    {
        $this->mappedRefProperties[$property] = $property;
        return $this;
    }

    /**
     * @param string $discriminatorField
     * @return self
     */
    public function setDiscriminatorField($discriminatorField)
    {
        $this->discriminatorField = ($discriminatorField !== null)? (string)$discriminatorField : null;
        return $this;
    }

    /**
     * Set the discriminator map
     *
     * @param   string[string]    $map  The mapping of class name to discriminator value
     */
    public function setDiscriminatorMap(array $map)
    {
        $this->discriminatorMap = [];

        foreach ($map as $class => $value) {
            $this->addToDiscriminatorMap($class, $value);
        }

        return $this;
    }

    /**
     * Add a class to the discriminator map
     *
     * @param string $class
     * @param string $value
     * @return self
     */
    public function addToDiscriminatorMap($class, $value)
    {
        $this->discriminatorMap[$class] = (string)$value;
        return $this;
    }

    /**
     * @param object $object
     * @param array $callbacks
     */
    protected function buildInsertDocument($object, array $document, array &$callbacks)
    {
        foreach ($this->mappedRefProperties as $property) {
            unset($document[$property]);
        }

        foreach ($this->aggregationProperties as $property => $persister) {
            if (!isset($document[$property])) {
                continue;
            }

            $callback = $persister->buildInsertDocument($document, $property, $document[$property]);

            if ($callback) {
                $callbacks[] = $callback;
            }
        }

        return $document;
    }

    /**
     * @param object $object
     * @param array $callbacks
     */
    protected function buildUpdateDocument($object, array $extractedData, EntityState $state, array &$callbacks)
    {
        // FIXME
    }

    /**
     * {@inheritDoc}
     * @see \Rampage\Nexus\MongoDB\PersistenceBuilder\PersistenceBuilderInterface::buildPersist()
     */
    public function buildPersist($object, EntityState &$state)
    {
        $callbacks = [];
        $data = $this->hydrator->extract($object);
        $id = isset($data['_id'])? $data['_id'] : null;

        if (!$id || ($state->getState() != EntityState::STATE_PERSISTED)) {
            $document = $this->buildInsertDocument($object, $data, $callbacks);
            $upsert = true;

            if (!$id) {
                $upsert = false;
                $document['_id'] = $this->createIdentity();
            }

            $state = new EntityState(EntityState::STATE_PERSISTED, $document, $document['_id']);
            return (new InvokableChain($callbacks))->prepend(function() use ($document, $upsert) {
                $this->collection->insert($document, $upsert);
            });
        }

        $document = $this->buildUpdateDocument($object, $data, $state, $callbacks);
        $state = new EntityState(EntityState::STATE_PERSISTED, $document, $document['_id']);

        return (new InvokableChain($callbacks))->prepend(function() use ($document, $id) {
            $this->collection->update(['_id' => $id], $document);
        });


    }

    /**
     * {@inheritDoc}
     * @see \Rampage\Nexus\MongoDB\PersistenceBuilder\PersistenceBuilderInterface::buildRemove()
     */
    public function buildRemove($object)
    {
        // TODO Auto-generated method stub

    }
}
