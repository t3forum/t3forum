<?php
namespace T3forum\T3forum\ViewHelpers\Bootstrap\Form;

/*
 * TYPO3 Forum Extension (EXT:t3forum)
 * https://github.com/t3forum
 *
 * COPYRIGHT NOTICE
 *
 * This extension was originally developed by
 * Mittwald CM Service GmbH & Co KG (https://www.mittwald.de)
 *
 * This script is part of the TYPO3 project. The TYPO3 project is free
 * software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any
 * later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html
 *
 * This script is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 */

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * ViewHelper that renders a form row.
 */
class RowViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * tagName
     *
     * @var string
     */
    protected $tagName = 'div';

    /**
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
    }

    /**
     *
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('llLabel', 'string', 'Locallang key for label.', false, '');
        $this->registerArgument('label', 'string', 'Hardcoded label (better to use llLabel instead).', false, '');
        $this->registerArgument('error', 'string', 'Error property path.', false);
        $this->registerArgument('errorLLPrefix', 'string', 'Error label locallang prefix.', false);
    }

    /**
     *
     */
    public function render()
    {
        $class = 'control-group';

        if ($this->arguments['llLabel']) {
            $label = LocalizationUtility::translate($this->arguments['llLabel'], 'typo3_forum');
        } else {
            $label = $this->arguments['label'];
        }

        if ($this->arguments['error']) {
            $results = $this->controllerContext->getRequest()->getOriginalRequestMappingResults()->getSubResults();

            $propertyPath = explode('.', $this->arguments['error']);
            foreach ($propertyPath as $currentPropertyName) {
                $errors = $this->getErrorsForProperty($currentPropertyName, $results);
            }

            if (count($errors) > 0) {
                $class .= ' error';
                $errorContent = '';
                foreach ($errors as $error) {
                    $errorText = LocalizationUtility::translate(
                        $this->arguments['errorLLPrefix'] . '_' . $error->getCode(),
                        't3forum'
                    );
                    if (!$errorText) {
                        $errorText = 'TRANSLATE: ' . $this->arguments['errorLLPrefix'] . '_' . $error->getCode();
                    }
                    $errorContent .= '<p class="help-block">' . $errorText . '</p>';
                }
            }
        } else {
            $errorText = '';
        }

        $label   = '<label>' . $label . '</label>';
        $content = '<div>' . $this->renderChildren() . $errorContent . '</div>';

        $this->tag->addAttribute('class', $class);
        $this->tag->setContent($label . $content);

        return $this->tag->render();
    }

    /**
     * Find errors for a specific property in the given errors array
     *
     * @param string $propertyName The property name to look up
     * @param array  $errors An array of Tx_Fluid_Error_Error objects
     * @return array An array of errors for $propertyName
     */
    protected function getErrorsForProperty($propertyName, $errors)
    {
        foreach ($errors as $name => $error) {
            if ($name === $propertyName) {
                return array_unique($error->getErrors());
            }
        }
        return [];
    }
}
