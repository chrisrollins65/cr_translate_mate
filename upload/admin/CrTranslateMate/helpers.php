<?php

function crHtmlentities($str)
{
    return htmlentities($str, ENT_COMPAT | ENT_HTML401, 'UTF-8');
}