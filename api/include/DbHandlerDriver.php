<?php

require_once 'LoggerHandler.php';
require_once 'MailSender.php';
require_once 'MailCreator.php';
require_once 'MapCreator.php';
require_once 'DistanceManager.php';

date_default_timezone_set('America/Bogota');

/**
 * Class to handle all db operations
 * This class will have CRUD methods for database tables
 *
 * @author Will Montiel
 */
class DbHandlerDriver {

    private $conn;
    private $androidAppVersion;

    function __construct() {
        require_once dirname(__FILE__) . '/DbConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }

    /**
     * Find the android app version
     */
    public function findAppVersion() {
        $log = new LoggerHandler();
        $stmt = $this->conn->prepare("SELECT idAndroidAppVersion, is_run_mode, name, uri_current, version_code_current, version_code_min, update_info, update_date FROM android_app_version ORDER BY idAndroidAppVersion DESC LIMIT 1");
        if ($stmt->execute()) {
            $stmt->bind_result($idAndroidAppVersion, $is_run_mode, $name, $uri_current, $version_code_current, $version_code_min, $update_info, $update_date);
            $stmt->fetch();
            $this->androidAppVersion = array(
                "idAndroidAppVersion" => $idAndroidAppVersion,
                "is_run_mode" => $is_run_mode,
                "name" => $name,
                "uri_current" => $uri_current,
                "version_code_current" => $version_code_current,
                "version_code_min" => $version_code_min,
                "update_info" => $update_info,
                "update_date" => $update_date,
            );
            $stmt->close();
            return true;
        } else {
            return false;
        }
    }

    public function getAppVersion() {
        return $this->androidAppVersion;
    }

    /**
     * Checking driver login
     * @param String $username User login username
     * @param String $password User login password
     * @return boolean User login status success/fail
     */
    public function checkLogin($username, $password) {
        // fetching user by email
        $stmt = $this->conn->prepare("SELECT clave FROM admin WHERE usuario = ? AND nivel_clte = 'conductor'");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($clave);
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            // Found user with the username
            // Now verify the password

            $stmt->fetch();
            $stmt->close();
            //if (PassHash::check_password($password_hash, $password)) {
            if ($password == $clave) {
                // User password is correct
                return TRUE;
            } else {
                // user password is incorrect
                return FALSE;
            }
        } else {
            $stmt->close();

            // user not existed with the email
            return FALSE;
        }
    }

    /**
     * Validating user api key
     * If the api key is there in db, it is a valid key
     * @param String $api_key user api key
     * @return boolean
     */
    public function isValidApiKey($api_key) {
        $stmt = $this->conn->prepare("SELECT id from admin WHERE api_key = ? AND nivel_clte = 'conductor'");
        $stmt->bind_param("s", $api_key);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    /**
     * Fetching user data by api key
     * @param String $api_key user api key
     */
    public function getUser($api_key) {
        $stmt = $this->conn->prepare("SELECT id, usuario, correo1, codigo, nombre, apellido, update_order FROM admin WHERE api_key = ? AND nivel_clte = 'conductor'");
        $stmt->bind_param("s", $api_key);
        if ($stmt->execute()) {
            $stmt->bind_result($user_id, $username, $email, $codigo, $nombre, $apellido, $update);
            $stmt->fetch();
            $user = array();
            $user["user_id"] = $user_id;
            $user["username"] = $username;
            $user["email"] = $email;
            $user["code"] = $codigo;
            $user["name"] = $nombre;
            $user["lastname"] = $apellido;
            $user["update"] = $update;
            // TODO
            // $user_id = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $user;
        } else {
            return NULL;
        }
    }

    /**
     * Fetching user by username
     * @param String $username User username
     */
    public function getUserByUsername($username) {
        $stmt = $this->conn->prepare("SELECT id, usuario, nombre, apellido, correo1, correo2, telefono1, telefono2, empresa, api_key, nivel_clte, codigo, first_time FROM admin WHERE usuario = ? AND estado = ? AND nivel_clte = 'conductor'");

        $status = "activo";
        $stmt->bind_param("ss", $username, $status);
        if ($stmt->execute()) {
            $stmt->bind_result($id, $username, $name, $lastname, $email1, $email2, $phone1, $phone2, $company, $api_key, $type, $code, $first_time);
            $stmt->fetch();
            $user = array();
            $user["id"] = $id;
            $user["username"] = $username;
            $user["name"] = $name;
            $user["lastname"] = $lastname;
            $user["email1"] = $email1;
            $user["email2"] = $email2;
            $user["phone1"] = $phone1;
            $user["phone2"] = $phone2;
            $user["type"] = $type;
            $user["company"] = $company;
            $user["api_key"] = $api_key;
            $user["code"] = $code;
            $user["first_time"] = $first_time;

            $stmt->close();
            return $user;
        } else {
            return NULL;
        }
    }

    /**
     * Busca un servicio pendiente
     * @param type $code
     * @return type
     */
    public function searchPendingService($code) {
        $log = new LoggerHandler();
        //$prevdate = date('m/d/Y', strtotime(date('Y-m-d'). ' -10 days'));
        //$nextdate = date('m/d/Y', strtotime(date('Y-m-d'). ' +30 days'));

        $prevdate = date('m/d/Y', strtotime(date('Y-m-d') . ' -10 days'));
        $nextdate = date('m/d/Y', strtotime(date('Y-m-d') . ' -1 days'));

        /*
          $stmt = $this->conn->prepare("SELECT
          o.id, o.fecha_s, o.hora_s1, o.hora_s2
          FROM orden AS o
          LEFT JOIN seguimiento AS s ON (s.referencia = o.referencia)
          WHERE o.conductor = ?
          AND STR_TO_DATE(o.fecha_s, '%m/%d/%Y') BETWEEN STR_TO_DATE('{$prevdate}', '%m/%d/%Y') AND STR_TO_DATE('{$nextdate}', '%m/%d/%Y')
          AND ((o.CD = NULL OR o.CD =  '') OR s.referencia IS NULL)
          ORDER BY o.id DESC
          LIMIT 1");
         */

        $stmt = $this->conn->prepare("SELECT 
                                            o.id, o.fecha_s, o.hora_s1, o.hora_s2, s.hora1, s.hora2
                                    FROM orden AS o 
                                            LEFT JOIN seguimiento AS s ON (s.referencia = o.referencia)
                                    WHERE o.conductor = ? 
                                            AND STR_TO_DATE(o.fecha_s, '%m/%d/%Y') BETWEEN STR_TO_DATE('{$prevdate}', '%m/%d/%Y') AND STR_TO_DATE('{$nextdate}', '%m/%d/%Y') 
                                            AND o.estado != 'cancelar'
                                            AND (s.referencia IS NULL OR s.hora1 IS NULL OR s.hora1 = '' OR s.hora2 = '' OR s.hora2 IS NULL)
                                    ORDER BY o.id ASC
                                    LIMIT 1");

        $stmt->bind_param("s", $code);

        $service = array();

        if ($stmt->execute()) {
            $stmt->bind_result($id, $fecha_s, $hora_s1, $hora_s2, $hora1, $hora2);
            $stmt->fetch();

            $old = 1;

            if ($id != 0) {
                $date = trim($fecha_s);

                $hoy = date("m/d/Y H:i");
                $thoy = date("H:i");

                $listD = explode("/", $date);

                $d = mktime($hora_s1, $hora_s2, 0, $listD[0], $listD[1], $listD[2]);

                $listD = explode("/", $hoy);
                $listT = explode(":", $thoy);
                $t = mktime($listT[0], $listT[1], 0, $listD[0], $listD[1], $listD[2]);
                

                if ($d > $t) {
                    $old = 0;
                }
            }

            $service["service_id"] = (empty($id) ? 0 : $id);
            $service["old"] = $old;

            $stmt->close();
        }

        return $service;
    }

    /**
     * 
     * @param type $id
     * @param type $code
     * @return string
     */
    public function getService($id, $code) {
        $log = new LoggerHandler();
        $ls = "";
        $db = $this->conn->prepare("SELECT placa FROM conductor WHERE codigo = '$code'");
        if ($db->execute()) {
            $db->bind_result($license_plate);
            $db->fetch();
            $ls = $license_plate;
            $db->close();
        }



        $sql = "SELECT o.id AS orden_id, 
                        o.ordencliente,
                        o.referencia,
                        o.fecha_e,
                        o.hora_e,
                        o.fecha_s,
                        o.hora_s1,
                        o.hora_s2,
                        o.hora_s3,
                        o.vuelo,
                        o.aerolinea,
                        o.cant_pax,
                        o.pax2,
                        o.pax3,
                        o.pax4,
                        o.pax5,
                        o.ciudad_inicio,
                        o.dir_origen,
                        o.ciudad_destino,
                        o.dir_destino,                                            
                        o.obaservaciones,
                        o.estado AS orden_estado,
                        o.CD,
                        p.id AS passenger_id,
                        p.codigo AS passenger_code,
                        p.nombre,
                        p.apellido,
                        p.telefono1,
                        p.telefono2,
                        p.correo1,
                        p.correo2,
                        p.empresa,
                        s.id as seguimiento_id,
                        s.b1ha,
                        s.bls,
                        s.pab,
                        s.st,
                        s.hora1,
                        s.hora2,
                        s.observaciones
            FROM orden AS o
                LEFT JOIN pasajeros AS p ON (p.codigo = o.persona_origen) 
                LEFT JOIN seguimiento as s ON (s.referencia = o.referencia)
            WHERE o.id = ?
            AND o.conductor = ? 
            AND o.estado != 'cancelar'";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param("is", $id, $code);

        $service = array();
        
        if ($stmt->execute()) {
            $stmt->bind_result($orden_id, $ordencliente, $referencia, $fecha_e, $hora_e, $fecha_s, $hora_s1, $hora_s2, $hora_s3, $vuelo, $aerolinea, 
                    $cant_pax, $pax2, $pax3, $pax4, $pax5, $ciudad_inicio, $dir_origen, $ciudad_destino, $dir_destino, $observaciones, 
                    $orden_estado, $cd, $passenger_id, $passenger_code, $name, $lastName, $phone1, $phone2, $email1, $email2, $company, 
                    $trace_id, $b1ha, $bls, $pab, $st, $hora1, $hora2, $sobs);

            $stmt->fetch();

            if (!empty($orden_id)) {
                $b1haTime = $b1ha;
                $blsTime = $bls;
                $pabTime = $pab;
                $stTime = $st;
                
                $b1haStatus = 0;
                $old = 1;
                //1. Calculamos la fecha de hoy en timestamp
                $now = time();

                if (!empty($fecha_s)) {
                    //2. Transformamos la fecha de inicio del servicio a timestamp
                    $startdate = explode("/", $fecha_s);
                    $sd = mktime($hora_s1, $hora_s2, 0, $startdate[0], $startdate[1], $startdate[2]);

                    //3. Le restamos dos horas a la fecha de inicio del servicio y transformamos a timestamp
                    $twoHoursBefore = $sd - 7200;

                    //4. Le sumamos 18 horas a la fecha de inicio del servicio y transformamos a timestamp
                    $hoursLater = strtotime("+18 hours", $sd);

                    if ($now >= $twoHoursBefore && $now <= $hoursLater) {
                        $b1haStatus = 1;
                    }
                }


                $b1ha = trim($b1ha);
                $bls = trim($bls);
                $pab = trim($pab);
                $st = trim($st);

                if ($b1haStatus == 1 && $fecha_s == date('m/d/Y')) {
                    $old = 0;
                } else if ($b1haStatus == 0 && !empty($b1ha) && $fecha_s == date('m/d/Y')) {
                    $old = 0;
                } else if ($now < $sd) {
                    $old = 0;
                }

                /*
                  if ($fecha_s == date('m/d/Y')) {
                  $old = 0;
                  }
                 */

                if (!empty($hora1) && !empty($hora2)) {
                    $b1haStatus = 1;
                    $b1ha = 1;
                    $bls = 1;
                    $pab = 1;
                    $st = 1;
                }



                $service["service_id"] = $orden_id;
                $service["ref"] = $referencia;
                $service["date"] = $fecha_e . " " . $hora_e;
                $service["sdate"] = $fecha_s;
                $service["start_time"] = $hora1;
                $service["end_time"] = $hora2;
                $service["start_date"] = $fecha_s . " " . $hora_s1 . ":" . $hora_s2;
                $service["service_start_date"] = $fecha_s;
                $service["start_date_nice"] = $this->getDateNice($fecha_s);
                $service["service_start_time"] = $hora_s1 . ":" . $hora_s2;
                $service["fly"] = $vuelo;
                $service["aeroline"] = $aerolinea;
                $service["pax_cant"] = (is_numeric($cant_pax) ? $cant_pax : 1);
                $service["pax"] = $this->getPassengers($pax2, $pax3, $pax4, $pax5);
                $service["source"] = trim($ciudad_inicio) . ", " . trim($dir_origen);
                $service["destiny"] = trim($ciudad_destino) . ", " . trim($dir_destino);
                $service["observations"] = trim($observaciones);
                $service["status"] = $orden_estado;
                $service["cd"] = $cd;
                $service['old'] = $old;
                $service["passenger_id"] = $passenger_id;
                $service["passenger_code"] = $passenger_code;
                $service["passenger_name"] = $name;
                $service["passenger_lastname"] = $lastName;
                $service["phone"] = trim($phone1) . ", " . trim($phone2);
                $service["email"] = trim($email1) . ", " . trim($email2);
                $service["email1"] = trim($email1);
                $service["email2"] = trim($email2);
                $service["company"] = trim($company);
                $service["event"] = trim($ordencliente);
                $service["license_plate"] = trim($ls);
                $service["trace_id"] = (empty($trace_id) ? 0 : $trace_id);
                
                $service["b1ha"] = (empty($b1ha) ? null : $b1ha);
                $service["b1haTime"] = $b1haTime;
                
                $service["bls"] = (empty($bls) ? null : $bls);
                $service["blsTime"] = $blsTime;
                
                $service["pab"] = (empty($pab) ? null : $pab);
                $service["pabTime"] = $pabTime;
                
                $service["st"] = (empty($st) ? null : $st);
                $service["stTime"] = $stTime;
                
                $service["b1haStatus"] = $b1haStatus;
                
                $service["tobservations"] = $sobs;

                $stmt->close();
            }
        } 
        
        return $service;
    }

    public function getServicesByDate($user, $date) {
        $sql = $this->getServicesSQL(true);

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param("sss", $date, $date, $user['code']);
        $stmt->execute();

        $services = $this->modelGroupedDataServices($stmt);

        //$services = $stmt->get_result();
        $stmt->close();
        return $services;
    }

    /**
     * 
     * @param type $pax2
     * @param type $pax3
     * @param type $pax4
     * @param type $pax5
     * @return type
     */
    private function getPassengers($pax2, $pax3, $pax4, $pax5) {
        $p = array();
        $pax = null;

        $pax2 = trim($pax2);
        $pax3 = trim($pax3);
        $pax4 = trim($pax4);
        $pax5 = trim($pax5);

        if (!empty($pax2) && $pax2 != "Seleccione una...") {
            $p[] = $pax2;
        }

        if (!empty($pax3) && $pax3 != "Seleccione una...") {
            $p[] = $pax3;
        }

        if (!empty($pax4) && $pax4 != "Seleccione una...") {
            $p[] = $pax4;
        }

        if (!empty($pax5) && $pax5 != "Seleccione una...") {
            $p[] = $pax5;
        }

        if (count($p) > 0) {
            $pax = implode(", ", $p);
        }

        return $pax;
    }

    public function getServicesGrouped($code) {
        $sql = $this->getServicesSQL(true);

        //$log = new LoggerHandler();
        //$log->writeString($sql);
        $stmt = $this->conn->prepare($sql);

        $currentDate = date('m/d/Y');
        $nextdate = date('m/d/Y', strtotime(date('Y-m-d') . ' + 30 days'));

        $stmt->bind_param("sss", $currentDate, $nextdate, $code);
        $stmt->execute();

        $services = $this->modelGroupedDataServices($stmt);

        $stmt->close();
        return $services;
    }

    private function getServicesSQL($between) {
        $date = ($between ? "STR_TO_DATE(o.fecha_s, '%m/%d/%Y') BETWEEN STR_TO_DATE(?, '%m/%d/%Y') AND STR_TO_DATE(?, '%m/%d/%Y') " : "o.fecha_s = ? ");

        $sql = "SELECT o.id AS orden_id, 
                        o.ordencliente,
                        o.referencia,
                        o.fecha_e,
                        o.hora_e,
                        o.fecha_s,
                        o.hora_s1,
                        o.hora_s2,
                        o.hora_s3,
                        o.vuelo,
                        o.aerolinea,
                        o.cant_pax,
                        o.pax2,
                        o.pax3,
                        o.pax4,
                        o.pax5,
                        o.ciudad_inicio,
                        o.dir_origen,
                        o.ciudad_destino,
                        o.dir_destino,                                            
                        o.obaservaciones,
                        o.estado AS orden_estado,
                        o.CD,
                        o.observaciones_cond,
                        p.id AS passenger_id,
                        p.codigo AS passenger_code,
                        p.nombre,
                        p.apellido,
                        p.telefono1,
                        p.telefono2,
                        p.correo1,
                        p.correo2,
                        p.empresa,
                        CONCAT(o.fecha_s, ' ', o.hora_s1 , ':', o.hora_s2) AS startDate
            FROM orden AS o
                LEFT JOIN pasajeros AS p ON (p.codigo = o.persona_origen) 
            WHERE {$date}
            AND o.conductor = ? 
            AND o.estado != 'cancelar'
            ORDER BY STR_TO_DATE(startDate, '%m/%d/%Y %H:%i') DESC";

        //AND (o.CD != null OR o.CD != '') ORDER BY o.id DESC LIMIT 20";

        return $sql;
    }

    private function modelGroupedDataServices($stmt) {
        $log = new LoggerHandler();
        $dates = array();
        $data = array(
            'dates' => array(),
            'services' => array(),
        );

        $stmt->bind_result($orden_id, $orden_cliente, $referencia, $fecha_e, $hora_e, $fecha_s, $hora_s1, $hora_s2, $hora_s3, $vuelo, $aerolinea, $cant_pax, $pax2, $pax3, $pax4, $pax5, $ciudad_inicio, $dir_origen, $ciudad_destino, $dir_destino, $observaciones, $orden_estado, $cd, $obsc, $passenger_id, $passenger_code, $name, $lastName, $phone1, $phone2, $email1, $email2, $company, $s);

        while ($stmt->fetch()) {
            $date = trim($fecha_s);

            $hoy = date("m/d/Y H:i");
            $thoy = date("H:i");
            
            $listD = explode("/", $date);
            
            $d = mktime($hora_s1, $hora_s2, 0, $listD[0], $listD[1], $listD[2]);

            $listD = explode("/", $hoy);
            $listT = explode(":", $thoy);
            $t = mktime($listT[0], $listT[1], 0, $listD[0], $listD[1], $listD[2]);

            $old = 1;

            if ($d > $t) {
                $old = 0;
            }

            $service = array();
            $service["service_id"] = $orden_id;
            $service["ref"] = $referencia;
            $service["date"] = $fecha_e . " " . $hora_e;
            $service["start_date"] = $fecha_s . " " . $hora_s1 . ":" . $hora_s2;
            $service["service_start_date"] = $fecha_s;
            $service["start_date_nice"] = $this->getDateNice($fecha_s);
            $service["service_start_time"] = $hora_s1 . ":" . $hora_s2;
            $service["fly"] = $vuelo;
            $service["aeroline"] = $aerolinea;
            $service["pax_cant"] = (is_numeric($cant_pax) ? $cant_pax : 1);
            $service["pax"] = $this->getPassengers($pax2, $pax3, $pax4, $pax5);
            $service["source"] = trim($ciudad_inicio) . ", " . trim($dir_origen);
            $service["destiny"] = trim($ciudad_destino) . ", " . trim($dir_destino);
            $service["observations"] = trim($observaciones);
            $service["status"] = $orden_estado;
            $service["cd"] = $cd;
            $service["driver_observations"] = $obsc;
            $service["old"] = $old;
            $service["company"] = trim($company);
            $service["event"] = trim($orden_cliente);
            $service["passenger_id"] = $passenger_id;
            $service["passenger_code"] = $passenger_code;
            $service["passenger_name"] = $name;
            $service["passenger_lastname"] = $lastName;
            $service["phone"] = trim($phone1) . ", " . trim($phone2);
            $service["email"] = trim($email1) . ", " . trim($email2);
            $service["trace_id"] = 0;
            $service["b1ha"] = null;
            $service["bls"] = null;
            $service["pab"] = null;
            $service["st"] = null;
            $service["end_time"] = null;
            $service["start_time"] = null;
            $service["b1haStatus"] = null;

            /*
            if (in_array($date, $dates)) {
                $key = array_search($date, $dates);
                $data['services'][$key][] = $service;
            } else {
                $newKey = count($dates);
                $dates[$newKey] = $date;
                $data['services'][$newKey][] = $service;
            }
            */
            if (in_array($service["start_date_nice"], $dates)) {
                $key = array_search($service["start_date_nice"], $dates);
                $data['services'][$key][] = $service;
            } else {
                $newKey = count($dates);
                $dates[$newKey] = $service["start_date_nice"];
                $data['services'][$newKey][] = $service;
            }
        }

        $data['dates'] = $dates;

        //$reversed = array_reverse($input);

        $data = array(
            'dates' => array_reverse($data['dates']),
            'services' => array_reverse($data['services']),
        );

        return $data;
    }

    function getDateNice($date) {
        $array = explode("/", $date);

        return $this->getMonth($array[0]) . " " . $array[1] . "/" . $array[2];
    }

    function getMonth($month) {
        if ($month == "01") {
            return "Ene";
        } else if ($month == "02") {
            return "Feb";
        } else if ($month == "03") {
            return "Mar";
        } else if ($month == "04") {
            return "Abr";
        } else if ($month == "05") {
            return "May";
        } else if ($month == "06") {
            return "Jun";
        } else if ($month == "07") {
            return "Jul";
        } else if ($month == "08") {
            return "Ago";
        } else if ($month == "09") {
            return "Sep";
        } else if ($month == "10") {
            return "Oct";
        } else if ($month == "11") {
            return "Nov";
        } else if ($month == "12") {
            return "Dic";
        }
    }

    public function changeServiceTime($user, $idOrden, $time) {
        $time = explode(":", $time);
        $service = $this->getService($idOrden, $user['code']);
        if (isset($service["service_id"]) && !$service["b1ha"]) {
            $log = $service["driver_observations"] . " ** El conductor " . $user['name'] . " " . $user['lastname'] . " cambia la hora del servicio de " . $service["start_time"] . " a {$time}";
            $stmt = $this->conn->prepare("UPDATE orden SET hora_s1 = ?, hora_s2 = ?, observaciones_cond = ? WHERE id = ?");
            $stmt->bind_param("sssi", $time[0], $time[1], $log, $idOrden);
            $stmt->execute();
            $num_affected_rows = $stmt->affected_rows;
            $stmt->close();
            return $num_affected_rows > 0;
        }

        return 0;
    }



    /**
     * 
     * @param type $code
     * @param type $idOrden
     * @param type $status
     * @return type
     */
    public function acceptOrDeclineService($code, $idOrden, $status) {
        if ($status == 1 || $status == "1") {
            $estado = date("D M j G:i:s T Y");
            $conductor = $code;
        } else {
            $estado = "";
            $conductor = "";
        }

        $stmt = $this->conn->prepare("UPDATE orden SET CD = ?, conductor = ? WHERE id = ?");

        $stmt->bind_param("ssi", $estado, $conductor, $idOrden);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    /**
     * 
     * @param type $id
     * @param type $code
     * @param type $start
     * @param type $end
     * @param type $image
     * @param type $observations
     */
    public function traceService($id, $user, $start, $end, $image, $observations, $version) {
        $log = new LoggerHandler();

        /*
        $log->writeArray($user);
        $log->writeString($id);
        $log->writeString($start);
        $log->writeString($end);
        $log->writeString($image);
        $log->writeString($observations);
        $log->writeString("Version: {$version}");
        */

        //1. Validamos que el servicio exista, y si es asi tomamos la referencia
        $reference = $this->validateServiceExists($id, $user['code']);

        //2. Aceptamos el servicio
        $this->acceptService($id, $user['code']);

        //3. Tomamos la placa del conductor
        $carLicense = $this->getCarLicense($user['code']);

        if (!empty($image)) {
            $uploaddir = '../../admin/informes/os/';
            $path = $uploaddir . $reference . ".jpg";

            if (!file_put_contents($path, base64_decode($image))) {
                throw new InvalidArgumentException('Error cargando la imagen al servidor, por favor contacta al administrador');
            }
        }

        if ($this->validateTraceExists($reference)) {
            return $this->setExistTrace($reference, $start, $end, $user, $observations, $carLicense, $version);
        }

        return $this->setTrace($reference, $start, $end, $user, $observations, $carLicense, $version);
    }

    /**
     * Delete a service trace
     * @param Array $user
     * @param int $id
     */
    public function deleteTrace($user, $id) {
        $num_affected_rows = 0;
        
//      $log = new LoggerHandler();
        $reference = $this->validateServiceExists($id, $user['code']);

        $stmt = $this->conn->prepare("DELETE FROM seguimiento WHERE referencia = ?");
        $stmt->bind_param("s", $reference);

        if ($stmt->execute()) {
            $num_affected_rows = $stmt->affected_rows;
            $stmt->close();
        }

        return $num_affected_rows > 0;
    }

    private function validateServiceExists($id, $code) {
        $stmt = $this->conn->prepare("SELECT referencia FROM orden WHERE id = ? AND conductor = ?");

        $stmt->bind_param("is", $id, $code);

        if ($stmt->execute()) {
            $stmt->bind_result($referencia);
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->fetch();
                $stmt->close();

                return $referencia;
            } else {
                $stmt->close();
                throw new InvalidArgumentException('No se encontró el servicio, por favor valida la información');
            }
        } else {
            $stmt->close();
            throw new InvalidArgumentException('No se encontró el servicio, por favor valida la información');
        }
    }

    public function validateServiceExistsById($id) {
        //$log = new LoggerHandler();
        $stmt = $this->conn->prepare("SELECT referencia FROM orden WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $stmt->bind_result($referencia);
            $stmt->fetch();
            $stmt->close();
            return $referencia;
        }
    }

    private function validateIfTraceExists($reference) {
        $stmt = $this->conn->prepare("SELECT id FROM seguimiento WHERE referencia = ?");
        $stmt->bind_param("s", $reference);

        if ($stmt->execute()) {
            $stmt->bind_result($id);
            $stmt->store_result();

            $rows = $stmt->num_rows;
            $stmt->close();

            return $rows;
        }
    }

    private function validateTraceExists($reference) {
        $stmt = $this->conn->prepare("SELECT id FROM seguimiento WHERE referencia = ?");
        $stmt->bind_param("s", $reference);

        $stmt->execute();
        $stmt->bind_result($id);
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->close();
            return true;
        }
        $stmt->close();
        return false;
    }

    private function acceptService($id, $code) {
        $estado = date("D M j G:i:s T Y");
        $stmt = $this->conn->prepare("UPDATE orden SET CD = ?, conductor = ? WHERE id = ?");
        $stmt->bind_param("ssi", $estado, $code, $id);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
    }

    private function getCarLicense($code) {
        $stmt = $this->conn->prepare("SELECT placa FROM conductor WHERE codigo = ?");
        $stmt->bind_param("s", $code);

        if ($stmt->execute()) {
            $stmt->bind_result($placa);
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->fetch();
                $stmt->close();

                return $placa;
            } else {
                $stmt->close();
                throw new InvalidArgumentException('No se encontró el servicio, por favor valida la información');
            }
        } else {
            $stmt->close();
            throw new InvalidArgumentException('No se encontró el servicio, por favor valida la información');
        }
    }

    private function setTrace($reference, $start, $end, $user, $observations, $carLicense, $version) {
        if ($start == "0" && $end == "0") {
            $start = date("H:i");
            $end = date("H:i");
        }

        $conductor = "{$user['name']} {$user['lastname']} ({$carLicense})";
        $elaborado = date("D, F d Y, H:i:s");
        $observations = (empty($observations) ? "SERVICIO SIN NOVEDAD" : $observations) . "(APP)";

        if ($this->validateTraceExists($reference)) {
            $stmt = $this->conn->prepare("UPDATE seguimiento SET hora1 = ?, hora2 = ?, conductor = ?, elaborado = ?, observaciones = ?, version = ? WHERE referencia = ?");
            $stmt->bind_param("sssssss", $start, $end, $conductor, $elaborado, $observations, $version, $reference);
        } else {
            $stmt = $this->conn->prepare("INSERT INTO seguimiento(referencia, hora1, hora2, conductor, elaborado, observaciones, version) VALUES(?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $reference, $start, $end, $conductor, $elaborado, $observations, $version);
        }

        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    private function setExistTrace($reference, $start, $end, $user, $observations, $carLicense, $version) {
        $log = new LoggerHandler();

        $times = "";

        $start = trim($start);
        $end = trim($end);

        if ($start == "0" && $end == "0") {
            $start = date("H:i");
            $end = date("H:i");
        }

        if (!empty($start) && !empty($end)) {
            $times = "hora1 = '{$start}', hora2 = '{$end}',";
        } else if (!empty($start)) {
            $times = "hora1 = '{$start}',";
        } else if (!empty($end)) {
            $times = "hora2 = '{$end}',";
        }

        $sql = "UPDATE seguimiento SET {$times} conductor = ?, elaborado = ?, observaciones = ?, version = ? WHERE referencia = ?";

        //$log->writeString("sql {$sql}");

        $stmt = $this->conn->prepare($sql);

        $conductor = "{$user['name']} {$user['lastname']} ({$carLicense})";
        $elaborado = date("D, F d Y, H:i:s");
        $observations = (empty($observations) ? "SERVICIO SIN NOVEDAD" : $observations) . "(APP)";

        $stmt->bind_param("sssss", $conductor, $elaborado, $observations, $version, $reference);

        $stmt->execute();

        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    /**
     * 
     * @param type $user
     * @param type $id
     */
    public function confirmService($user, $id) {
        //1. Validamos que el servicio exista, y si es asi tomamos la referencia
        $reference = $this->validateServiceExists($id, $user['code']);

        //2. Tomamos la placa del auto
        $carLicense = $this->getCarLicense($user['code']);

        //3. Cambiamos el estado de la orden a reconfirmacion = 1 y reconfirmacion2 = "si"
        $this->reconfirmService($id);
        /*
          if (!$this->reconfirmService($id)) {
          $log->writeString("No se encontró el servicio: {$id}");
          throw new InvalidArgumentException('No se encontró el servicio, por favor valida la información');
          }
         */

        //4. Guardamos el seguimiento con el estado B1HA
        return $this->saveB1HAStatus($reference, $user, $carLicense);
    }

    private function reconfirmService($id) {
        $now = date("d/m/Y H:s");
        $stmt = $this->conn->prepare("UPDATE orden SET reconfirmacion = 1, reconfirmacion2 = 'si', reconfirmacion_fecha = '{$now}' WHERE id = ?");
        $stmt->bind_param("i", $id);

        if (!$stmt->execute()) {
            throw new Exception('Ocurrió un error, contacta al administrador');
        }

        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    private function saveB1HAStatus($reference, $user, $carLicense) {
        $log = new LoggerHandler();
        if (!$this->validateTraceExists($reference)) {
            $stmt = $this->conn->prepare("INSERT INTO seguimiento(referencia, conductor, b1ha) VALUES(?, ?, ?)");

            $conductor = "{$user['name']} {$user['lastname']} ({$carLicense})";
            $b1ha = date("d/m/Y H:i:s");

            $stmt->bind_param("sss", $reference, $conductor, $b1ha);
            $result = $stmt->execute();

            if ($result) {
                $stmt->close();
                return true;
            } else {
                $stmt->close();
                return false;
            }
        } else {
            $stmt = $this->conn->prepare("UPDATE seguimiento SET conductor = ?, b1ha = ? WHERE referencia = ?");

            $conductor = "{$user['name']} {$user['lastname']} ({$carLicense})";
            $b1ha = date("d/m/Y H:i:s");

            $stmt->bind_param("sss", $conductor, $b1ha, $reference);
            $result = $stmt->execute();

            if ($result) {
                $stmt->close();
                return true;
            } else {
                $log->writeString("Error " . $stmt->error);
                $stmt->close();
                return false;
            }
        }
    }

    /**
     * 
     * @param type $user
     * @param type $idOrden
     */
    public function setOnSource($user, $idOrden) {
        //1. Validamos que el servicio exista, y si es asi tomamos la referencia
        $reference = $this->validateServiceExists($idOrden, $user['code']);

        //3. Guardamos el seguimiento con el estado BLS
        return $this->saveBLSStatus($reference);
    }

    public function saveBLSStatus($reference) {
        $stmt = $this->conn->prepare("UPDATE seguimiento SET bls = ? WHERE referencia = ?");

        $bls = date("d/m/Y H:i:s");

        $stmt->bind_param("ss", $bls, $reference);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    /**
     * 
     * @param type $user
     * @param type $id
     * @param type $lat
     * @param type $lon
     */
    public function setPreLocation($user, $id, $lat, $lon) {
        //1. Validamos que el servicio exista, y si es asi tomamos la referencia
        $reference = $this->validateServiceExists($id, $user['code']);

        //2. Guardamos la latitud y longitud en la tabla location
        if (!$this->savePreLocation($id, $reference, $lat, $lon)) {
            return 0;
        }

        $obj = $this->getServiceStatus($reference);

        if (empty($obj->b1ha) && empty($obj->bls) && empty($obj->pab) && empty($obj->st)) {
            return 0;
        }

        if (!empty($obj->b1ha) && empty($obj->bls)) {
            return 1;
        }

        return 0;
    }

    private function getServiceStatus($reference) {
        $obj = new stdClass();
        $obj->b1ha = "";
        $obj->bls = "";
        $obj->pab = "";
        $obj->st = "";

        $stmt = $this->conn->prepare("SELECT b1ha, bls, pab, st FROM seguimiento WHERE referencia = ?");
        $stmt->bind_param("s", $reference);
        if ($stmt->execute()) {
            $stmt->bind_result($b1ha, $bls, $pab, $st);
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $stmt->fetch();

                $obj->b1ha = $b1ha;
                $obj->bls = $bls;
                $obj->pab = $pab;
                $obj->st = $st;

                $stmt->close();

                return $obj;
            }
        }
        return $obj;
    }

    private function savePreLocation($id, $reference, $lat, $lon) {
        $log = new LoggerHandler();
        $stmt = $this->conn->prepare("INSERT INTO prelocation(idOrden, referencia, latitude, longitude, createdon) VALUES(?, ?, ?, ?, ?)");

        $createdon = date("d/m/Y H:i:s");
        $stmt->bind_param("issss", $id, $reference, $lat, $lon, $createdon);
        $result = $stmt->execute();

        if ($result) {
            $stmt->close();
            return true;
        } else {
            $log->writeString("Error " . $stmt->error);
            $stmt->close();
            return false;
        }
    }

    /**
     * 
     * @param type $user
     * @param type $idOrden
     */
    public function startService($user, $idOrden) {
        //1. Validamos que el servicio exista, y si es asi tomamos la referencia
        $reference = $this->validateServiceExists($idOrden, $user['code']);

        //2. Guardamos la hora de inicio del segumiento
        return $this->saveStartTimeService($reference);
    }

    public function saveStartTimeService($reference) {
        $stmt = $this->conn->prepare("UPDATE seguimiento SET hora1 = ?, pab = ? WHERE referencia = ?");

        $pab = date("d/m/Y H:i:s");
        $start = date("H:i");

        $stmt->bind_param("sss", $start, $pab, $reference);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    /**
     * Save a location with latitude and longitude
     * @param type $user
     * @param type $id
     * @param type $lat
     * @param type $lon
     */
    public function setLocation($user, $id, $lat, $lon) {
//    $log = new LoggerHandler();
        //1. Validamos que el servicio exista, y si es asi tomamos la referencia
        $reference = $this->validateServiceExists($id, $user['code']);

        //2. Guardamos la latitud y longitud en la tabla location
        if (!$this->saveLocation($id, $reference, $lat, $lon)) {
            return 0;
        }

        $obj = $this->getServiceStatus($reference);

        if (empty($obj->b1ha) && empty($obj->bls) && empty($obj->pab) && empty($obj->st)) {
            return 0;
        }

        if (!empty($obj->pab) && empty($obj->st)) {
            return 1;
        }



        return 0;
    }

    private function saveLocation($id, $reference, $lat, $lon) {
        $log = new LoggerHandler();
        $stmt = $this->conn->prepare("INSERT INTO location(idOrden, referencia, latitude, longitude, createdon) VALUES(?, ?, ?, ?, ?)");

        $createdon = date("d/m/Y H:i:s");
        $stmt->bind_param("issss", $id, $reference, $lat, $lon, $createdon);
        $result = $stmt->execute();

        if ($result) {
            $stmt->close();
            return true;
        } else {
            $log->writeString("Error " . $stmt->error);
            $stmt->close();
            return false;
        }
    }

    /**
     * 
     * @param type $user
     * @param type $id
     * @param type $observations
     */
    public function finishService($user, $id, $observations, $image, $version) {
//    $log = new LoggerHandler();
        try {
            //1. Validamos que el servicio exista, y si es asi tomamos la referencia
            $reference = $this->validateServiceExists($id, $user['code']);
            $mailSender = new MailSender();

            //3. Actualizamos el seguimiento con la hora de finalización y demás datos
            if ($this->saveEndTimeService($reference, $user, $observations, $version)) {

                if (!empty($image)) {
                    $uploaddir = '../../admin/informes/os/';
                    $path = $uploaddir . $reference . ".jpg";

                    if (!file_put_contents($path, base64_decode($image))) {
                        throw new InvalidArgumentException('Error cargando la imagen al servidor, por favor contacta al administrador');
                    }
                }


                $serviceArray = $this->getService($id, $user['code']);

                $email1 = $serviceArray["email1"];

                if (empty($email1)) {
                    $log = new LoggerHandler();
                    $log->writeString("Se finalizó el servicio exitosamente, pero no se pudo enviar el resumen al cliente porque este no tiene correo, {$reference}");
//          throw new InvalidArgumentException("Se finalizó el servicio exitosamente, pero no se pudo enviar el resumen al cliente porque este no tiene correo");
                } else if (!filter_var($email1, FILTER_VALIDATE_EMAIL)) {
                    $log = new LoggerHandler();
                    $log->writeString("Se finalizó el servicio exitosamente, pero no se pudo enviar el resumen al cliente porque este no tiene un correo valido, {$reference}");
                    //throw new InvalidArgumentException("Se finalizó el servicio exitosamente, pero no se pudo enviar el resumen al cliente, por correo invalido");
                } else {
                    $service = new stdClass();
                    $service->id = $id;
                    $service->mapUrl = "";
                    $service->name = $serviceArray['passenger_name'] . " " . $serviceArray['passenger_lastname'];
                    $service->reference = $reference;
                    $service->date = $serviceArray['sdate'];
                    $service->startTime = $serviceArray['start_time'];
                    $service->source = $serviceArray['source'];
                    $service->destiny = $serviceArray['destiny'];
                    $service->endTime = $serviceArray['end_time'];
                    $service->driverName = $user['name'] . " " . $user['lastname'];
                    $service->driverCode = $user['code'];

                    $mailCreator = new MailCreator();
                    $mail = new stdClass();
                    $data = new stdClass();

                    $mapCreator = new MapCreator();
                    $mapCreator->setReference($reference);
                    $points = $mapCreator->findLocationPoints($id);

                    if (count($points) > 0) {
                        $p = implode("|", $points);
                        $start = $points[0];
                        $end = $points[count($points) - 1];
//            $url = $mapCreator->getMapUrl($start, $end, $p);
                        $url = $mapCreator->createMap($reference, $start, $end, $p);
                        $service->source = ($mapCreator->getAddressByLatIng($points[0]) != null ? $mapCreator->getAddressByLatIng($points[0]) : $serviceArray['source']);
                        $service->destiny = ($mapCreator->getAddressByLatIng($points[count($points) - 1]) != null ? $mapCreator->getAddressByLatIng($points[count($points) - 1]) : $serviceArray['destiny']);

                        $email1 = $serviceArray["email1"];
                        
                        $distance = $mapCreator->getDistance();
                        
                        $service->distance = $distance['distance'];
//                        $service->distance = 1;
//                        $service->time = $distance['time'];
                        
                        
                        $dm = new DistanceManager();
                        $service->time = $dm->getTimeDiff($service->startTime, $service->endTime);
//                        $service->time = 1;
                        
                        $service->mapUrl = $url;
                        $mailCreator->createResumeNotification($service);
                        $mail->html = $mailCreator->getHtml();
                        $mail->plaintext = $mailCreator->getPlaintext();

                        $data->subject = "Resumen de su servicio con Transportes Ejecutivos({$reference}) {$serviceArray['start_date']}";
                        $data->from = array('info@transportesejecutivos.com' => 'Transportes Ejecutivos');
                        $data->to = array($email1 => $service->name);

                        $this->saveServiceResumeHtml($id, $reference, $mail->html);

                        $mailSender->setMail($mail);
                        $mailSender->sendMail($data);
                    }

                    $mailCreator->createResumeNotificationForDriver($service);

                    $mailDriver = new stdClass();
                    $mailDriver->html = $mailCreator->getHtml();
                    $mailDriver->plaintext = $mailCreator->getPlaintext();

                    $data->subject = "Resumen del servicio con Transportes Ejecutivos({$reference}) {$serviceArray['start_date']}";
                    $data->from = array('info@transportesejecutivos.com' => 'Transportes Ejecutivos');
                    $data->to = array($user['email'] => $service->driverName);

                    $mailSender->setMail($mailDriver);
                    $mailSender->sendMail($data);
                }
            } else {
                throw new InvalidArgumentException("No se pudo finalizar el servicio, por favor intenta de nuevo");
            }
        } catch (InvalidArgumentException $ex) {
            throw new InvalidArgumentException($ex->getMessage());
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    private function saveEndTimeService($reference, $user, $observations, $version) {
        $stmt = $this->conn->prepare("UPDATE seguimiento SET hora2 = ?, elaborado = ?, observaciones = ?, st = ?, version = ? WHERE referencia = ?");

        $end = date("H:i");
        $elaborado = date("D, F d Y, H:i:s");
        $observations = (empty($observations) ? "SERVICIO SIN NOVEDAD" : $observations) . "(APP)";
        $st = date("d/m/Y H:i:s");

        $stmt->bind_param("ssssss", $end, $elaborado, $observations, $st, $version, $reference);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    public function saveServiceResumeHtml($idOrden, $ref, $content) {
        $log = new LoggerHandler();
        $stmt = $this->conn->prepare("INSERT INTO service_resume(idServiceResume, idOrden, reference, mailContent, createdon) VALUES(null, ?, ?, ?, ?)");
        $createdon = date("d/m/Y H:i:s");
        $stmt->bind_param("isss", $idOrden, $ref, $content, $createdon);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        $log->writeString("Error " . $stmt->error);
        $stmt->close();
        return false;
    }

    public function setQualify($id, $ref, $points, $comments) {
        $log = new LoggerHandler();
        $c = (empty($comments) ? "Sin comentarios" : $comments);
        $stmt = $this->conn->prepare("INSERT INTO survey(idOrden, referencia, puntos, comentarios, fecha) VALUES(?, ?, ?, ?, ?)");
        $createdon = date("d/m/Y H:i:s");
        $stmt->bind_param("isiss", $id, $ref, $points, $c, $createdon);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        $log->writeString("Error " . $stmt->error);
        $stmt->close();
        return false;
    }

    public function updateQualify($id, $points, $comments) {
        //$log = new LoggerHandler();
        $c = (empty($comments) ? "Sin comentarios" : $comments);
        $stmt = $this->conn->prepare("UPDATE survey SET puntos = ?, comentarios = ? WHERE idOrden = ?");
        $stmt->bind_param("isi", $points, $c, $id);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        $stmt->close();
        return false;
    }

}