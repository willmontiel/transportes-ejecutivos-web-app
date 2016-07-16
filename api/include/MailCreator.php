<?php

require_once 'LoggerHandler.php';

class MailCreator {
    public $html;
    public $plaintext;
    
    public function __construct() {
        
    }
    
    public function getHtml() {
        return $this->html;
    }
    
    public function getPlaintext() {
        return $this->plaintext;
    }

    public function createDeclineServiceNotification($data) {
        $this->html = "";
    }
    
    public function createAcceptServiceNotification($data) {
        $this->html = "";
    }
    
    public function createConfirmNotification($data) {
        $this->html = "";
    }
    
    public function createOnSourceNotification($data) {
        $this->html = "";
    }
    
    public function createResumeNotification($data) {
        $this->html = '<table style="background-color:#dddddd;width:100%">
                       <tbody>
                          <tr>
                             <td style="padding:20px">
                                <center>
                                   <table style="width:600px" width="600px" cellspacing="0" cellpadding="0">
                                      <tbody>
                                         <tr>
                                            <td style="width:100%;vertical-align:top;padding:0;background-color:transparent;border-top-left-radius:0px;border-top-right-radius:0px;border-bottom-right-radius:0px;border-bottom-left-radius:0px;border-color:#ffffff;border-style:none;border-width:0px">
                                               <table style="table-layout:fixed;width:100%;border-spacing:0px" width="100%" cellpadding="0">
                                                  <tbody>
                                                     <tr>
                                                        <td style="padding-left:0px;padding-right:0px">
                                                           <table style="border-color:#ffffff;border-style:none;border-width:0px;background-color:transparent;border-top-left-radius:0px;border-top-right-radius:0px;border-bottom-right-radius:0px;border-bottom-left-radius:0px;margin-top:10px;margin-bottom:10px;width:100%;border-spacing:0px" cellpadding="0" width="100%">
                                                              <tbody>
                                                                 <tr>
                                                                    <td style="width:100%;vertical-align:middle;padding-left:0px;padding-right:0px" width="100%">
                                                                       <table style="border-color:#ffffff;border-style:none;border-width:0px;background-color:transparent;border-top-left-radius:0px;border-top-right-radius:0px;border-bottom-right-radius:0px;border-bottom-left-radius:0px;margin-top:0px;margin-bottom:0px;width:100%;border-spacing:0px" cellpadding="0" width="100%">
                                                                          <tbody>
                                                                             <tr>
                                                                                <td align="center" style="width:432px" width="432px"><img src="https://ci5.googleusercontent.com/proxy/y7MS6w5cJZ4z1S_6BiN1tpl-W7lt7dkY8PJCl6_ipzquNtrcUR5QlsClStNdjlcPU_72fxGwH5KhfclAwNPBwNenid40K32R_3P28xYkvJrU=s0-d-e1-ft#http://transportesejecutivos.com/images/horizontal_logo.png" alt="horizontal_logo.png" style="min-height:63px;width:432px" height="63" width="432" class="CToWUd"></td>
                                                                             </tr>
                                                                          </tbody>
                                                                       </table>
                                                                    </td>
                                                                 </tr>
                                                              </tbody>
                                                           </table>
                                                        </td>
                                                     </tr>
                                                  </tbody>
                                               </table>
                                            </td>
                                         </tr>
                                         <tr>
                                            <td style="width:100%;vertical-align:top;padding:0;background-color:#ffffff;border-top-left-radius:8px;border-top-right-radius:8px;border-bottom-right-radius:0px;border-bottom-left-radius:0px;border-color:#ffffff;border-style:none;border-width:0px">
                                               <table style="table-layout:fixed;width:100%;border-spacing:0px" width="100%" cellpadding="0">
                                                  <tbody>
                                                     <tr>
                                                        <td style="padding-left:0px;padding-right:0px">
                                                           <table style="border-color:#ffffff;border-style:none;border-width:0px;background-color:transparent;border-top-left-radius:0px;border-top-right-radius:0px;border-bottom-right-radius:0px;border-bottom-left-radius:0px;margin-top:0px;margin-bottom:0px;width:100%;border-spacing:0px" cellpadding="0" width="100%">
                                                              <tbody>
                                                                 <tr>
                                                                    <td style="width:100%;padding-left:0px;padding-right:0px" width="100%">
                                                                       <table style="border-color:#ffffff;border-style:none;border-width:0px;background-color:transparent;border-top-left-radius:0px;border-top-right-radius:0px;border-bottom-right-radius:0px;border-bottom-left-radius:0px;margin-top:0px;margin-bottom:0px;width:100%" cellpadding="0" width="100%">
                                                                          <tbody>
                                                                             <tr>
                                                                                <td style="word-break:break-word;padding:15px 15px;font-family:Helvetica,Arial,sans-serif">
                                                                                   <h3 style="text-align:center">Gracias por elegir nuestro servicio Sr(a) ' . $data->name . '</h3>
                                                                                </td>
                                                                             </tr>
                                                                          </tbody>
                                                                       </table>
                                                                    </td>
                                                                 </tr>
                                                              </tbody>
                                                           </table>
                                                        </td>
                                                     </tr>
                                                    
                                                     <tr>
                                                        <td style="padding-left:0px;padding-right:0px">
                                                           <table style="border-color:#ffffff;border-style:none;border-width:0px;background-color:transparent;border-top-left-radius:0px;border-top-right-radius:0px;border-bottom-right-radius:0px;border-bottom-left-radius:0px;margin-top:0px;margin-bottom:0px;width:100%;border-spacing:0px" cellpadding="0" width="100%">
                                                              <tbody>
                                                                 <tr>
                                                                    <td style="width:100%;padding-left:0px;padding-right:0px" width="100%">
                                                                       <table style="border-color:#ffffff;border-style:none;border-width:0px;background-color:transparent;border-top-left-radius:0px;border-top-right-radius:0px;border-bottom-right-radius:0px;border-bottom-left-radius:0px;margin-top:0px;margin-bottom:0px;width:100%" cellpadding="0" width="100%">
                                                                          <tbody>
                                                                             <tr>
                                                                                <td style="word-break:break-word;padding:15px 15px;font-family:Helvetica,Arial,sans-serif">
                                                                                   <h4 style="text-align:center"><span style="color:rgb(127,127,127)">Este es el resumen del servicio de referencia ' . $data->reference . ' </span></h4>
                                                                                </td>
                                                                             </tr>
                                                                          </tbody>
                                                                       </table>
                                                                    </td>
                                                                 </tr>
                                                              </tbody>
                                                           </table>
                                                        </td>
                                                     </tr>
                                                  </tbody>
                                               </table>
                                            </td>
                                         </tr>
                                         <tr>
                                            <td style="width:100%;vertical-align:top;padding:0;background-color:#ffffff;border-top-left-radius:0px;border-top-right-radius:0px;border-bottom-right-radius:0px;border-bottom-left-radius:0px;border-color:#ffffff;border-style:none;border-width:0px">
                                               <table style="table-layout:fixed;width:100%;border-spacing:0px" width="100%" cellpadding="0">
                                                  <tbody>
                                                     <tr>
                                                        <td style="padding-left:0px;padding-right:0px">
                                                           <table style="border-color:#ffffff;border-style:none;border-width:0px;background-color:transparent;border-top-left-radius:0px;border-top-right-radius:0px;border-bottom-right-radius:0px;border-bottom-left-radius:0px;margin-top:0px;margin-bottom:0px;width:100%;border-spacing:0px" cellpadding="0" width="100%">
                                                              <tbody>
                                                                 <tr>
                                                                    <td style="width:50%;vertical-align:middle;padding-left:0px;padding-right:0px" width="50%">
                                                                       <table style="border-color:#ffffff;border-style:none;border-width:0px;background-color:transparent;border-top-left-radius:0px;border-top-right-radius:0px;border-bottom-right-radius:0px;border-bottom-left-radius:0px;margin-top:0px;margin-bottom:0px;width:100%;border-spacing:0px" cellpadding="0" width="100%">
                                                                          <tbody>
                                                                             <tr>
                                                                                <td align="center" style="width:300px" width="300px"><img src="' . $data->mapUrl . '" alt="map_b256658b-4442-44db-9a39-e887775d9c78.jpg" style="min-height:228px;width:300px" height="228" width="300" class="CToWUd"></td>
                                                                             </tr>
                                                                          </tbody>
                                                                       </table>
                                                                    </td>
                                                                    <td style="width:50%;padding-left:0px;padding-right:0px" width="50%">
                                                                       <table style="border-color:#ffffff;border-style:none;border-width:0px;background-color:transparent;border-top-left-radius:0px;border-top-right-radius:0px;border-bottom-right-radius:0px;border-bottom-left-radius:0px;margin-top:0px;margin-bottom:0px;width:100%" cellpadding="0" width="100%">
                                                                          <tbody>
                                                                             <tr>
                                                                                <td style="word-break:break-word;padding:15px 15px;font-family:Helvetica,Arial,sans-serif">
                                                                                   <p><strong>Fecha:</strong> ' . $data->date . '</p>
                                                                                   <p><strong>Hora de inicio:</strong> ' . $data->startTime . '</p>
                                                                                   <p><strong>Origen:</strong> ' . $data->source . '</p>
                                                                                   <p><strong>Destino:</strong> ' . $data->destiny . ' </p>
                                                                                   <p><strong>Hora de finalización:</strong> ' . $data->endTime . '</p>
                                                                                </td>
                                                                             </tr>
                                                                          </tbody>
                                                                       </table>
                                                                    </td>
                                                                 </tr>
                                                              </tbody>
                                                           </table>
                                                        </td>
                                                     </tr>
                                                     
                                                     <tr>
                                                        <td style="padding-left:0px;padding-right:0px">
                                                           <table style="border-color:#ffffff;border-style:none;border-width:0px;background-color:transparent;border-top-left-radius:0px;border-top-right-radius:0px;border-bottom-right-radius:0px;border-bottom-left-radius:0px;margin-top:20px;margin-bottom:0px;width:100%;border-spacing:0px" cellpadding="0" width="100%">
                                                              <tbody>
                                                                 <tr>
                                                                    <td style="width:100%;padding-left:0px;padding-right:0px" width="100%">
                                                                       <table style="border-color:#ffffff;border-style:none;border-width:0px;background-color:transparent;border-top-left-radius:0px;border-top-right-radius:0px;border-bottom-right-radius:0px;border-bottom-left-radius:0px;margin-top:0px;margin-bottom:0px;width:100%" cellpadding="0" width="100%">
                                                                          <tbody>
                                                                             <tr>
                                                                                <td style="word-break:break-word;padding:15px 15px;font-family:Helvetica,Arial,sans-serif">
                                                                                   <p style="text-align:center">Viajó con ' . $data->driverName . '</p>
                                                                                </td>
                                                                             </tr>
                                                                          </tbody>
                                                                       </table>
                                                                    </td>
                                                                 </tr>
                                                              </tbody>
                                                           </table>
                                                        </td>
                                                     </tr>
                                                     <tr>
                                                        <td style="padding-left:0px;padding-right:0px">
                                                           <table style="border-color:#ffffff;border-style:none;border-width:0px;background-color:transparent;border-top-left-radius:0px;border-top-right-radius:0px;border-bottom-right-radius:0px;border-bottom-left-radius:0px;margin-top:0px;margin-bottom:0px;width:100%;border-spacing:0px" cellpadding="0" width="100%">
                                                              <tbody>
                                                                 <tr>
                                                                    <td style="width:100%;vertical-align:middle;padding-left:0px;padding-right:0px" width="100%">
                                                                       <table style="border-color:#ffffff;border-style:none;border-width:0px;background-color:transparent;border-top-left-radius:0px;border-top-right-radius:0px;border-bottom-right-radius:0px;border-bottom-left-radius:0px;margin-top:0px;margin-bottom:0px;width:100%;border-spacing:0px" cellpadding="0" width="100%">
                                                                          <tbody>
                                                                             <tr>
                                                                                <td align="center" style="width:80px; border-radius: 50%;" width="80px"><img src="http://www.transportesejecutivos.com/app/conductores/logos/redimensionar_logo.php?imagen=cara' . $data->driverCode . '.jpg&amp;ancho=80" alt="x.png" style="min-height:80px;width:80px;border-radius: 50%;" height="80" width="80" class="CToWUd"></td>
                                                                                <td align="center" style="width:80px; border-radius: 50%;" width="80px"><img src="http://www.transportesejecutivos.com/app/conductores/logos/redimensionar_logo.php?imagen=carro' . $data->driverCode . '.jpg&amp;ancho=80" alt="x.png" style="min-height:80px;width:80px;border-radius: 50%;" height="80" width="80" class="CToWUd"></td>
                                                                             </tr>
                                                                          </tbody>
                                                                       </table>
                                                                    </td>
                                                                 </tr>
                                                              </tbody>
                                                           </table>
                                                        </td>
                                                     </tr>
                                                     <tr>
                                                        <td style="padding-left:0px;padding-right:0px">
                                                           <table style="border-color:#ffffff;border-style:none;border-width:0px;background-color:transparent;border-top-left-radius:0px;border-top-right-radius:0px;border-bottom-right-radius:0px;border-bottom-left-radius:0px;margin-top:15px;margin-bottom:0px;width:100%;border-spacing:0px" cellpadding="0" width="100%">
                                                              <tbody>
                                                                 <tr>
                                                                    <td style="padding-left:0px;padding-right:0px">
                                                                       <table style="border-color:#ffffff;border-style:none;border-width:0px;background-color:transparent;border-top-left-radius:0px;border-top-right-radius:0px;border-bottom-right-radius:0px;border-bottom-left-radius:0px;margin-top:0px;margin-bottom:0px;width:100%" cellpadding="0" width="100%">
                                                                          <tbody>
                                                                             <tr>
                                                                                <td>
                                                                                   <hr>
                                                                                </td>
                                                                             </tr>
                                                                          </tbody>
                                                                       </table>
                                                                    </td>
                                                                 </tr>
                                                              </tbody>
                                                           </table>
                                                        </td>
                                                     </tr>
                                                  </tbody>
                                               </table>
                                            </td>
                                         </tr>
                                         <tr>
                                            <td style="width:100%;vertical-align:top;padding:0;background-color:#ffffff;border-top-left-radius:0px;border-top-right-radius:0px;border-bottom-right-radius:0px;border-bottom-left-radius:0px;border-color:#ffffff;border-style:none;border-width:0px">
                                               <table style="table-layout:fixed;width:100%;border-spacing:0px" width="100%" cellpadding="0">
                                                  <tbody>
                                                     <tr>
                                                        <td style="padding-left:0px;padding-right:0px">
                                                           <table style="border-color:#ffffff;border-style:none;border-width:0px;background-color:transparent;border-top-left-radius:0px;border-top-right-radius:0px;border-bottom-right-radius:0px;border-bottom-left-radius:0px;margin-top:20px;margin-bottom:20px;width:100%;border-spacing:0px" cellpadding="0" width="100%">
                                                              <tbody>
                                                                 <tr>
                                                                    <td style="text-align:center;word-break:break-word;padding:15px 15px;font-family:Helvetica,Arial,sans-serif">
                                                                            <span style="color:rgb(127,127,127)">
                                                                                    Por Favor, Califique Nuestro Servicio
                                                                            </span>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="text-align:center;word-break:break-word;padding:15px 15px;font-family:Helvetica,Arial,sans-serif">
                                                                       <span style="font-size:11px;width:20px!important;min-height:20px;display:inline-block!important;padding:0px 2px">
                                                                            <a style="color:#2ba6cb;text-decoration:none" target="_blank" href="http://www.transportesejecutivos.com/survey/rating.php?id=' . $data->id . '&rating=1">
                                                                                <img style="outline:none;text-decoration:none;float:left;clear:both;display:block;width:20px;border:none" align="left" src="http://www.transportesejecutivos.com/images/rating.png">
                                                                            </a>
                                                                        </span>

                                                                        <span style="font-size:11px;width:20px!important;min-height:20px;display:inline-block!important;padding:0px 2px">
                                                                            <a style="color:#2ba6cb;text-decoration:none" target="_blank" href="http://www.transportesejecutivos.com/survey/rating.php?id=' . $data->id . '&rating=2">
                                                                                <img style="outline:none;text-decoration:none;float:left;clear:both;display:block;width:20px;border:none" align="left" src="http://www.transportesejecutivos.com/images/rating.png">
                                                                            </a>
                                                                        </span>

                                                                        <span style="font-size:11px;width:20px!important;min-height:20px;display:inline-block!important;padding:0px 2px">
                                                                            <a style="color:#2ba6cb;text-decoration:none" target="_blank" href="http://www.transportesejecutivos.com/survey/rating.php?id=' . $data->id . '&rating=3">
                                                                                <img style="outline:none;text-decoration:none;float:left;clear:both;display:block;width:20px;border:none" align="left" src="http://www.transportesejecutivos.com/images/rating.png">
                                                                            </a>
                                                                        </span>

                                                                        <span style="font-size:11px;width:20px!important;min-height:20px;display:inline-block!important;padding:0px 2px">
                                                                            <a style="color:#2ba6cb;text-decoration:none" target="_blank" href="http://www.transportesejecutivos.com/survey/rating.php?id=' . $data->id . '&rating=4">
                                                                                <img style="outline:none;text-decoration:none;float:left;clear:both;display:block;width:20px;border:none" align="left" src="http://www.transportesejecutivos.com/images/rating.png">
                                                                            </a>
                                                                        </span>

                                                                        <span style="font-size:11px;width:20px!important;min-height:20px;display:inline-block!important;padding:0px 2px">
                                                                            <a style="color:#2ba6cb;text-decoration:none" target="_blank" href="http://www.transportesejecutivos.com/survey/rating.php?id=' . $data->id . '&rating=5">
                                                                                <img style="outline:none;text-decoration:none;float:left;clear:both;display:block;width:20px;border:none" align="left" src="http://www.transportesejecutivos.com/images/rating.png">
                                                                            </a>
                                                                        </span>
                                                                    </td>
                                                                 </tr>
                                                              </tbody>
                                                           </table>
                                                        </td>
                                                     </tr>
                                                  </tbody>
                                               </table>
                                            </td>
                                         </tr>
                                      </tbody>
                                   </table>
                                </center>
                             </td>
                          </tr>
                       </tbody>
                    </table>';
        
        $this->plaintext = "Resumen de servicio con Transportes Ejecutivos con referencia: {$data->referencia}";
    }
}

