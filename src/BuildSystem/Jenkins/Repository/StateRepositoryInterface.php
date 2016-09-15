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

namespace Rampage\Nexus\BuildSystem\Jenkins\Repository;

use Rampage\Nexus\BuildSystem\Jenkins\PackageScanner\InstanceConfig;
use Rampage\Nexus\BuildSystem\Jenkins\Job;
use Rampage\Nexus\BuildSystem\Jenkins\Build;

/**
 * Defines the state repository for jenkins scanners
 */
interface StateRepositoryInterface
{
    /**
     * Returns all processed builds for the given job and instance
     *
     * @param InstanceConfig $config
     * @param Job $job
     * @return int[]
     */
    public function getProcessedBuilds(InstanceConfig $config, Job $job);

    /**
     * Add a build as processed
     *
     * @param InstanceConfig $config
     * @param Build $build
     */
    public function addProcessedBuild(InstanceConfig $config, Build $build);
}
