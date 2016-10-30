<?php
/**
 * Created by PhpStorm.
 * User: yoon2
 * Date: 2016. 9. 30.
 * Time: 오후 5:26
 */

function markdown($markdown)
{
 return   app(ParsedownExtra::class)->text($markdown);

}