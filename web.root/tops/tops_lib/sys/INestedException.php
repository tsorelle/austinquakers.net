<?php
/** Class: INestedException ***************************************/
/// Interface for exceptions that have inner exceptions
/*****************************************************************/
interface INestedException {
	public function getInnerException() ;
}

