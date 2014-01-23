<?php
/**
 * This is part of rampage-nexus
 * Copyright (c) 2013 Axel Helmert
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
 * @copyright Copyright (c) 2013 Axel Helmert
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License
 */

namespace rampage\nexus;

use SplFileInfo;

/**
 * Interface for implementing application packages
 */
interface ApplicationPackageInterface
{
    /**
     * Returns the application name
     */
    public function getName();

    /**
     * Returns the application label
     *
     * @return string
     */
    public function getLabel();

    /**
     * Returns the version number of this package
     *
     * @return string
     */
    public function getVersion();

    /**
     * Returns the defined deplyoment parameters
     *
     * @return DeployParameter[]
     */
    public function getParameters();

    /**
     * Returns the icon for the application
     *
     * @return resource|\SplFileInfo|false
     */
    public function getIcon();

    /**
     * Returns the license for this application
     *
     * @return string|resource|\SplFileInfo|false
     */
    public function getLicense();

    /**
     * Install this application package for the given application
     *
     * @param Application $application
     * @return self
     */
    public function install(entities\Application $application);
}
