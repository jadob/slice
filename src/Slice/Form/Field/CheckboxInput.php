<?php

namespace Slice\Form\Field;

use Slice\Form\Exception\FormException;

/**
 * Description of CheckboxInput
 *
 * @author pizzaminded <miki@appvende.net>
 */
class CheckboxInput extends AbstractInput {

    public function setPlaceholder() {
        throw new FormException('You cannot set placeholder text on checkbox input.');
    }

}
