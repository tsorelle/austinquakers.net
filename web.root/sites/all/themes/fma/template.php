<?php

function fma_preprocess_block(&$variables) {
    $block = $variables['block'];
    $title = $block->title;
    if ($title == '<user short name>') {
        $title = TUser::GetShortName();
        $block->title = $title;
        $block->subject = $title;
        $variables['block'] = $block;
    }
    else if (substr($title,0,10) == '<collapse>') {
        $block = $variables['block'];
        $parts = split('>',$title);
        $title = $parts[1];
        $content =
            '<fieldset class="collapsible collapsed"><legend>'.
            $title.'... </legend>'.$block->content.'</fieldset>';
        TTracer::Trace(' fma_preprocess_block - '.$title);
        TTracer::Trace("Block Title is $title");
        $block->title = $title;
        $block->subject = '';
        $block->content = $content;
        $variables['block'] = $block;
    }

}

function fma_preprocess_node(&$variables) {
    $node = $variables['node'];
    if ($node->type == 'event') {

        if (!user_is_logged_in() &&  $node->field_public_event[0]['value'] == 'no') {
            $variables['teaser'] = 'You must be logged in to view this content.';
            $variables['content'] = 'You must be logged in to view this content.';
        }
        else {
            $teaserText =  $variables['teaser'];
            $repeatPos = strpos($teaserText, 'Repeats ');
            if ($repeatPos) {
                $untilPos = strpos($teaserText,'until',$repeatPos);
                if ($untilPos) {
                    $endPos = strpos($teaserText,'.',$untilPos);
                    if ($endPos) {
                        $result =
                            substr($teaserText,0,$repeatPos + $untilPos).
                            substr($teaserText,$repeatPos + $untilPos + $endPos);
                        TTracer::Trace("Event teaser:&nbsp;$result");
                    }
                }
            }
        }
    }
}
