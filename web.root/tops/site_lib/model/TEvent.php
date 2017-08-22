<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/4/2015
 * Time: 2:55 PM
 */
class TEvent
{

    public static function getTaxonomyTermId($termName) {

    }
    public static function getEventTaxonomy($nodeId)
    {
        $sql =
            "SELECT term_data.tid, vocabulary.name, term_data.name ".
            "FROM node ".
            "LEFT JOIN term_node ON node.vid = term_node.vid ".
            "LEFT JOIN term_data ON term_node.tid = term_data.tid ".
            "LEFT JOIN vocabulary ON term_data.vid = vocabulary.vid ".
            "WHERE  node.nid = ? ";

        $statement = TSqlStatement::ExecuteDrupalQuery($sql, 'i', $nodeId);
        $termId=0;$vocabulary='';$termName='';
        $statement->instance->bind_result($termId,$vocabulary,$termName);
        $result = array();
        while ($statement->Next()) {
            $term = new stdClass();
            $term->id = $termId;
            $term->vocabulary = $vocabulary;
            $term->name = $termName;
            $result[$termId] = $term;
        }
        return new TTaxonomy($result);
    }

    private static function buildEventQuery($where, $dateFormat=null) {
        $result =
            'SELECT node.nid AS eventId, '.
            // "node.vid AS sessionId, ".
            'timeField.delta as sessionid, '.
            "node.title AS title, revisions.teaser AS bodyText, ".
            "field_event_type_value AS eventType, field_location_value as location, ".
            "field_public_event_value as isPublic, field_outside_group_event_value as forOutsideGroup, ".
            "DATE_FORMAT(timeField.field_time_value, '%W %M %e, %Y') AS startDate, ".
            "DATE_FORMAT(timeField.field_time_value2, '%W %M %e, %Y') AS endDate, ".
            "DATE_FORMAT(timeField.field_time_value, '%h:%i %p') AS startTime, ".
            "DATE_FORMAT(timeField.field_time_value2, '%h:%i %p') AS endTime, ".
            "timeField.field_time_rrule AS repeatRule, ".
            "IF(INSTR(revisions.body,'<!--break-->') > 0,1,0) AS moreInfo ".
            "FROM node ".
            "LEFT JOIN content_field_time timeField ON node.vid = timeField.vid ".
            "LEFT JOIN content_type_event eventFields ON node.vid = eventFields.vid ".
            "LEFT JOIN node_revisions revisions ON node.vid = revisions.vid WHERE ".$where;

        if ($dateFormat) {
            $result = str_replace('%W %M %e, %Y',$dateFormat,$result);
        }
        return $result;
    }

    private static function getEvent($eventId, $sessionId = null, $dateFormat = '%b %e, %Y') {
        if (empty($sessionId)) {
            $dates = self::getEventDates($eventId);
            if (empty($dates)) {
                return null;
            }
            $sessionId = $dates[0]->sessionId;
        }
        $sql = self::buildEventQuery('node.nid = ? AND timeField.delta = ?',$dateFormat);
        $statement = TSqlStatement::ExecuteDrupalQuery($sql, 'ii', $eventId,$sessionId);
        $result = self::getResults($statement,true);
        $taxonomy = self::getEventTaxonomy($eventId);
        $meetingService = $taxonomy->getTerm('Event Category','Meeting service opportunity');
        if ($meetingService) {
            $result->eventType = 'task';
        }
        return $result;
    }

    /**
     * @param $eventId
     * @param null $dateFormat
     * @return stdClass
     *
     * Get event but not persons
     * Used by InitializeEventInvitationCommand
     */
    public static function getEventInfo($eventId, $dateFormat = null) {
        $result = new stdClass();
        $result->event = self::getEvent($eventId,null,$dateFormat);
        if (!$result->event) {
            $result = self::createNullEvent($eventId);
        }
        return $result;
    }

    public static function getNodeTitle ($nid)
    {
        $sql = "SELECT node.title AS title FROM node WHERE node.nid = ?";
        $title = TSqlStatement::ExecuteScalerForDrupal($sql,'i',$nid);
        return empty($title)? '' : $title;
    }

    /**
     * @param $eventId
     * @param null $sessionId
     * @param null $dateFormat
     * @return stdClass
     *
     * Return event object with person array.
     */
    public static function getEventAndPersons($eventId, $sessionId=null,$dateFormat = null) {
        $result = new stdClass();
        $result->event = self::getEvent($eventId,$sessionId,$dateFormat);
        if ($result->event) {
            $result->persons = self::GetEventPersons($eventId);
        }
        else {
            return self::createNullEvent($eventId);
        }
        return $result;
    }

    private static function getEventDates($eventId) {
        $sql =
            "SELECT node.nid AS eventId, ".
            // "node.vid AS sessionId, ".
            "timeField.delta as sessionId, ".
            "DATE_FORMAT(timeField.field_time_value, '%b %e, %Y') AS startDate,".
            "DATE_FORMAT(timeField.field_time_value, '%h:%i %p') AS startTime ".
            "FROM node LEFT JOIN content_field_time timeField ON node.vid = timeField.vid ".
            "LEFT JOIN node_revisions revisions ON node.vid = revisions.vid ".
            "WHERE DATE_FORMAT(timeField.field_time_value, '%Y-%m-%d') >= DATE_FORMAT(CURRENT_DATE, '%Y-%m-%d') ".
            "AND node.nid = ?";

        $statement = TSqlStatement::ExecuteDrupalQuery($sql, 'i', $eventId);

        $result = array();
        $eventId = 0;
        $sessionId = 0;
        $startDate = '';
        $startTime = '';

        $statement->instance->bind_result(
            $eventId,
            $sessionId,
            $startDate,
            $startTime
        );

        while ($statement->next()) {
            $event = new stdClass();
            $event->eventId = $eventId;
            $event->sessionId = $sessionId;
            $event->startDate = $startDate;
            $event->startTime = $startTime;
            array_push($result,$event);
        }
        return $result;
    }


    private static function getResults($statement,$singleInstance = false) {
        $result = array();
        $eventId = 0;
        $sessionId = 0;
        $title = '';
        $bodyText = '';
        $startDate = '';
        $startTime = '';
        $endDate = '';
        $endTime = '';
        $repeatRule = '';
        $moreInfo = 0;
        $eventType = '';
        $location = '';
        $isPublic = '';
        $forOutsideGroup = '';


        $statement->instance->bind_result(
            $eventId,
            $sessionId,
            $title,
            $bodyText,
            $eventType,
            $location,
            $isPublic,
            $forOutsideGroup,
            $startDate,
            $endDate,
            $startTime,
            $endTime,
            $repeatRule,
            $moreInfo
        );


        while ($statement->next()) {
            $event = new stdClass();
            $event->eventId = $eventId;
            $event->sessionId = $sessionId;
            $event->title = $title;
            $event->description = $bodyText;
            $event->when = self::formatTime($startDate,$startTime,$endDate,$endTime);
            $event->repeatInfo = $repeatRule;
            $event->eventType = $eventType;
            $event->location =  $location;
            $event->moreInfo = $moreInfo;
            $event->startDate = $startDate;
            $event->startTime = $startTime;
            $event->endDate = $endDate;
            $event->endTime = $endTime;
            $event->isPublic =  strtolower($isPublic)  == 'yes' ? 1 : 0;
            $event->forOutsideGroup = strtolower($forOutsideGroup) == 'yes' ? 1 : 0;
            if ($singleInstance) {
                return $event;
            }
            array_push($result,$event);
        }
        return $result;
    }

    /**
     * @param $eventId
     * @return array
     * @throws DatabaseException
     *
     * Used internally and by Update reminder command
     */
    public static function GetEventPersons($eventId)
    {
        $sql =

            "SELECT  p.personId, CONCAT(p.firstName,' '," .
            "CASE WHEN (p.middleName IS NULL OR p.middleName = '') THEN '' ELSE CONCAT(p.middleName,' ') END, p.lastName) AS fullName, ".
            'p.email '.
            'FROM reminders r JOIN persons p ON r.personId = p.personID '.
            'WHERE eventId = ?';


        $statement = TSqlStatement::ExecuteQuery($sql,'i',$eventId);
        $personId = 0;
        $name = '';
        $email = '';
        $statement->instance->bind_result($personId,$name,$email);
        $result = array();
        while ($statement->next()) {
            $person = new stdClass();
            $person->personId = $personId;
            $person->name = $name;
            $person->email = $email;
            array_push($result,$person);
        }
        return $result;
    }

    private static function createNullEvent($eventId = null)
    {
        $result = new stdClass();
        $eventInfo = new stdClass();
        $eventInfo->eventId = $eventId;
        $eventInfo->title = ($eventId) ? self::getNodeTitle($eventInfo->eventId) : '';
        $eventInfo->when = $eventInfo->title ? 'This event as already occured.': '';
        $eventInfo->sessionId = 0;
        $eventInfo->bodyText = '';
        $eventInfo->startDate = null;
        $eventInfo->startTime = null;
        $eventInfo->endDate = null;
        $eventInfo->endTime = null;
        $eventInfo->repeatRule = '';
        $eventInfo->moreInfo = 0;
        $eventInfo->eventType = '';
        $eventInfo->location = '';
        $eventInfo->isPublic = 0;
        $eventInfo->forOutsideGroup = 0;
        $result->event = $eventInfo;
        $result->persons = array();
        return $result;
    }

    /**
     * @param int $days
     * @return array
     * @throws DatabaseException
     *
     * used by reminder script
     */
    public static function getFutureEvents($days = 2) {
        $result = array();
        $sql =
            'SELECT DISTINCT node.nid AS eventId, '.
            // "node.vid as sessionId, ".
            'evtSchedule.delta as sessionId, '.
            'node.title,evtSchedule.field_time_value  AS startTime '.
            'FROM node LEFT JOIN content_type_event eventFields ON node.vid = eventFields.vid '.
            'LEFT JOIN content_field_time evtSchedule ON node.vid = evtSchedule.vid '.
            'WHERE evtSchedule.field_time_value > DATE_ADD(CURDATE(),INTERVAL '.$days.' DAY) '.
            'AND  evtSchedule.field_time_value < DATE_ADD(CURDATE(),INTERVAL '.($days + 1).' DAY) '.
            "AND eventFields.field_outside_group_event_value <> 'yes'";

        $statement = TSqlStatement::ExecuteDrupalQuery($sql);
        $eventId=0;$sessionId=0;$title='';$startTime = '';
        $statement->instance->bind_result($eventId,$sessionId, $title,$startTime);
        $result = array();
        while ($statement->next()) {
            $reminder = new stdClass();
            $reminder->eventId = $eventId;
            $reminder->sessionId = $sessionId;
            $reminder->title = $title;
            $reminder->startTime = $startTime;
            array_push($result,$reminder);
        }

        return $result;
    }

    private static function formatTime($startDate,$startTime,$endDate,$endTime)
    {
        $result = $startDate;
        if (!empty($startTime)) {
            $result .= ',  ' . $startTime;
            if ((empty($endDate) && empty($endTime)) || ($startDate == $endDate && $startTime == $endTime)) {
                return $result;
            }
            $result .= ' to ';
            if ((!empty($endDate)) && $endDate != $startDate) {
                $result .= $endDate . ' ';
            }
            if (!empty($endTime) && $endTime != $startTime) {
                $result .= $endTime;
            }
        }
        return $result;
    }

    /**
     * @param null $dateFormat
     * @return array
     * @throws DatabaseException
     *
     * Used by meetingServicesViewModel via GetUpcomingTasksCommand
     */
    public static function getTaskSchedule($dateFormat=null)
    {
        $sql =
            "SELECT DISTINCT node.nid AS eventId, node.title AS title, revisions.teaser AS description, " .
            "field_event_type_value AS eventType, " .
            "DATE_FORMAT(timeField.field_time_value, '%Y-%m-%d') AS startDate, " .
            "DATE_FORMAT(timeField.field_time_value2, '%Y-%m-%d') AS endDate, " .
            "DATE_FORMAT(timeField.field_time_value, '%h:%i %p') AS startTime, " .
            "DATE_FORMAT(timeField.field_time_value2, '%h:%i %p') AS endTime " .
            "FROM node LEFT JOIN content_field_time timeField ON node.vid = timeField.vid " .
            "LEFT JOIN content_type_event eventFields ON node.vid = eventFields.vid " .
            "LEFT JOIN node_revisions revisions ON node.vid = revisions.vid " .
            "LEFT JOIN term_node ON node.vid = term_node.vid ".
            "LEFT JOIN term_data ON term_node.tid = term_data.tid ".
            "WHERE timeField.`field_time_value` >= CURDATE() ".
            "AND (field_event_type_value = 'task' OR term_data.name = 'Meeting service opportunity') ".
            "ORDER BY timeField.field_time_value ASC, timeField.field_time_value2 ASC ";

        if ($dateFormat) {
            $sql = str_replace('%Y-%m-%d',$dateFormat,$sql);
        }

        $statement = TSqlStatement::ExecuteDrupalQuery($sql);
        $result = array();
        $eventId = 0;   $title = '';$description = ''; $startDate = ''; $startTime = ''; $endDate = '';  $endTime = ''; $eventType = '';

        $statement->instance->bind_result(
            $eventId,
            $title,
            $description,
            $eventType,
            $startDate,
            $endDate,
            $startTime,
            $endTime
        );


        while ($statement->next()) {
            $event = new stdClass();
            $event->eventId = $eventId;
            $event->title = $title;
            $event->when = self::formatTime($startDate,$startTime,$endDate,$endTime);
            $event->description = TText::HtmlToFlatText($description);
            array_push($result,$event);
        }
        return $result;
    }

    public static function getRoomAndResourceList()
    {
        $sql = 'SELECT tid AS termId, name, description FROM term_data WHERE vid = 3';
        $statement = TSqlStatement::ExecuteDrupalQuery($sql);
        $result = array();
        $value=0;$text='';$title='';
        $statement->instance->bind_result($value,$text,$title);
        while ($statement->next()) {
            $item = new stdClass();
            $item->value = $value;
            $item->text = $text;
            $item->title = $title;
            array_push($result,$item);
        }

        return $result;
    }

    public static function getUpcomingRoomAndResourceUsage($resourceTermId)
    {
        $sql = "SELECT node.nid AS eventId, node.title AS title, " .
            "DATE_FORMAT(timeField.field_time_value, '%W %M %e, %Y') AS startDate, " .
            "DATE_FORMAT(timeField.field_time_value2, '%W %M %e, %Y') AS endDate, " .
            "DATE_FORMAT(timeField.field_time_value, '%h:%i %p') AS startTime, " .
            "DATE_FORMAT(timeField.field_time_value2, '%h:%i %p') AS endTime, " .
            "timeField.field_time_value AS start,  " .
            "timeField.field_time_value2 AS end  " .
            "FROM node LEFT JOIN content_field_time timeField ON node.vid = timeField.vid " .
            "LEFT JOIN content_type_event eventFields ON node.vid = eventFields.vid " .
            "LEFT JOIN node_revisions revisions ON node.vid = revisions.vid " .
            "LEFT JOIN term_node term_node ON node.vid = term_node.vid " .
            "WHERE node.status = 1 AND node.type = 'event' " .
            "AND timeField.`field_time_value` >= CURDATE() " .
            "AND term_node.tid = ? " .
            "ORDER BY timeField.field_time_value ASC, timeField.field_time_value2 ASC ";

        $statement = TSqlStatement::ExecuteDrupalQuery($sql, 'i', $resourceTermId);
        $result = array();
        $eventId = 0;
        $title = '';
        $description = '';
        $startDate = '';
        $startTime = '';
        $endDate = '';
        $endTime = '';
        $start = '';
        $end = '';

        $statement->instance->bind_result(
            $eventId,
            $title,
            $startDate,
            $endDate,
            $startTime,
            $endTime,
            $start,
            $end
        );


        while ($statement->next()) {
            $event = new stdClass();
            $event->eventId = $eventId;
            $event->title = $title;
            $event->when = self::formatTime($startDate, $startTime, $endDate, $endTime);
            $event->start = $start;
            $event->end = $end;
            array_push($result, $event);
        }

        return $result;

    }

}