<?php
/**
 * ParticipantQuery.Get API - queries civicrm_participant directly
 */
function civicrm_api3_participant_query_get($params) {
  $event_id = CRM_Utils_Array::value('event_id', $params);
  $limit = CRM_Utils_Array::value('limit', $params, 100);
  
  if (!$event_id) {
    return civicrm_api3_create_error('event_id is required');
  }
  
  $sql = "SELECT p.id, p.contact_id, p.event_id, p.status_id, p.register_date, 
          p.source, p.fee_amount, p.registered_by_id,
          c.display_name, c.sort_name,
          e.email, ph.phone
          FROM civicrm_participant p
          LEFT JOIN civicrm_contact c ON p.contact_id = c.id
          LEFT JOIN civicrm_email e ON c.id = e.contact_id AND e.is_primary = 1
          LEFT JOIN civicrm_phone ph ON c.id = ph.contact_id AND ph.is_primary = 1
          WHERE p.event_id = %1
          LIMIT %2";
  
  $queryParams = array(
    1 => array($event_id, 'Integer'),
    2 => array($limit, 'Integer'),
  );
  
  $dao = CRM_Core_DAO::executeQuery($sql, $queryParams);
  $result = array();
  while ($dao->fetch()) {
    $result[] = array(
      'id' => $dao->id,
      'contact_id' => $dao->contact_id,
      'display_name' => $dao->display_name,
      'email' => $dao->email,
      'phone' => $dao->phone,
      'event_id' => $dao->event_id,
      'status_id' => $dao->status_id,
      'fee_amount' => $dao->fee_amount,
      'source' => $dao->source,
      'register_date' => $dao->register_date,
    );
  }
  
  return civicrm_api3_create_success($result, $params, 'ParticipantQuery', 'get');
}
