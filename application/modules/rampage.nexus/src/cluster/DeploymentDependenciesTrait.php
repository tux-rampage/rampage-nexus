<?php
/**
 * LICENSE: $license_text$
 *
 * @author    Axel Helmert <ah@luka.de>
 * @copyright Copyright (c) 2014 LUKA netconsult GmbH (www.luka.de)
 * @license   $license$
 */

namespace rampage\nexus\cluster;

use rampage\nexus\DeployStrategyManager;
use rampage\nexus\PackageStorage;
use rampage\nexus\orm\DeploymentRepository;
use rampage\nexus\orm\DeploymentRepositoryAwareTrait;

use Zend\ServiceManager\ServiceLocatorInterface;


trait DeploymentDependenciesTrait
{
    use DeploymentRepositoryAwareTrait;

    /**
     * @var DeployStrategyManager
     */
    protected $deployStrategyManager;

    /**
     * @var PackageStorage
     */
    protected $packageStoreage;

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return \rampage\nexus\cluster\DeploymentDependenciesTrait
     */
    protected function setDependenciesFromServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->setDeployStrategyManager($serviceLocator->get(DeployStrategyManager::class))
            ->setPackageStoreage($serviceLocator->get(PackageStorage::class))
            ->setDeploymentRepository($serviceLocator->get(DeploymentRepository::class));

        return $this;
    }

    /**
     * @param DeployStrategyManager $manager
     * @return \rampage\nexus\cluster\DeploymentDependenciesTrait
     */
    public function setDeployStrategyManager(DeployStrategyManager $manager)
    {
        $this->deployStrategyManager = $manager;
        return $this;
    }

    /**
     * @param \rampage\nexus\PackageStorage $packageStoreage
     * @return self
     */
    public function setPackageStoreage(PackageStorage $packageStoreage)
    {
        $this->packageStoreage = $packageStoreage;
        return $this;
    }
}
