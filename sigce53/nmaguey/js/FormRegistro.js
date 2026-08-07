// Importa la biblioteca Vue 3 desde CDN
const { computed,ref,nextTick, defineComponent,onMounted, watch, toRefs} = Vue;

// Define el componente Vue con Composition API
const ClientesForm = defineComponent({
  template: `
  <div id="modalContinua" class="modal fade" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog  modal-lg">
      <div class="modal-content">
        <div class="modal-header" >
          <button type="button" class="close" data-dismiss="modal" style="color: white" aria-label="Cerrar" @click="closeModal"><span aria-hidden="true">×</span></button>
          <h4 v-if="registroNuevo.id==null || registroNuevo.id==0">Registro de Predio</h4>
          <h4 v-else>Asignación de Atributos de Maguey, Predio <b>{{registroNuevo.paraje}} ({{registroNuevo.id_paraje}})</b></h4>
        </div>
        <div class="modal-body" style="max-height: calc(100vh - 210px); overflow-y: auto; overflow-x: auto;">
          <form class="form-horizontal" action="/action_page.php">
            <!--<h4>Datos del Predio o Vivero</h4>-->
            <ul class="nav nav-tabs" id="tabDetalleA">
              <!--<li class="active"><a href="#tabGeneralesA" data-toggle="tab">Datos Generales</a></li>-->
              <!--<li><a href="#Guias" data-toggle="tab">Guías de Maguey</a></li>-->
              <li class="active"><a href="#Atributos" data-toggle="tab">Atributos</a></li>
            </ul>
            <div class="tab-content rellenosup">
                <div class="tab-pane" id="tabGeneralesA">
                    <div class="col-sm-12">
                      <div class="form-group">
                        <div class="col-sm-6">
                            <label class="control-label">No Control:</label>
                            <input type="text" class='form-control mayus' v-model="registroNuevo.id_cliente" placeholder="Ingrese el Número de Control" autocomplete="off" required id="id_cliente" />
                        </div>

                        <div class="col-sm-6">
                            <label class="control-label">Nombre:</label>
                            <input type="text" class='form-control mayus' v-model="registroNuevo.nombre" placeholder="Nombre del Cliente" autocomplete="off" required disabled />
                        </div>
                      </div>
                    </div>

                    <div class="col-sm-6" >
                      <div class="form-group">
                          <div class="col-sm-6">
                              <label class="control-label">Registro:</label>
                              <select class="form-control nuevo_cmb prod_cmb" v-model="registroNuevo.maguey_con_registro">
                                <option value=null selected props="disabled" disabled>Selecciona</option>
                                <option value="1">EN SITIO</option>
                                <option value="2">DOCUMENTAL</option>
                              </select>
                          </div>

                          <div class="col-sm-6" >
                              <label class="control-label">Servicio:</label>
                              <select class="form-control nuevo_cmb prod_cmb" v-model="registroNuevo.servicio" :disabled="!(registroNuevo.maguey_con_registro == '2')">
                                <option value=null selected props="disabled" disabled>Selecciona</option>
                                <option value="NORMAL">NORMAL</option>
                                <option value="EXCLUSIVO">EXCLUSIVO</option>
                              </select>
                          </div>
                      </div>
                    </div>
                    <div class="col-sm-6" >
                      <div class="form-group">
                          <div class="col-sm-3">
                              <label class="control-label">Propietario:</label>
                              <select class="form-control nuevo_cmb prod_cmb" v-model="registroNuevo.propietario">
                                <option value=null selected props="disabled" disabled>Selecciona</option>
                                <option value="1">SI</option>
                                <option value="2">NO</option>
                              </select>
                          </div>
                          <!--:value="(registroNuevo.propietario === '1' || registroNuevo.propietario === '' || registroNuevo.propietario === null) ? registroNuevo.nombre: '' " -->
                          <div class="col-sm-9">
                              <label class="control-label">Nombre Completo:</label>
                              <input type="text" class='form-control mayus' v-model="registroNuevo.nombrep" autocomplete="off" 
                              :disabled="registroNuevo.propietario === '1' || registroNuevo.propietario === '' || registroNuevo.propietario === null " required 
                              />
                          </div>
                      </div>
                    </div>

                    <div class="col-sm-12">
                      <div class="form-group">
                        <div class="col-sm-3">
                            <label class="control-label">Fecha de Registro:</label>
                            <input type="date" class='form-control mayus' v-model="registroNuevo.fecha" placeholder="Ingrese el Nombre del Predio" autocomplete="off" required />
                        </div>
                        <div class="col-sm-3">
                            <label class="control-label">Nombre del Predio:</label>
                            <input type="text" class='form-control mayus' v-model="registroNuevo.paraje" placeholder="Ingrese el Nombre del Predio" autocomplete="off" required />
                        </div>
                        <div class="col-sm-3">
                          <label class="control-label">Representante en Campo:</label>
                          <input type="text" class='form-control mayus' v-model="registroNuevo.rcampo" placeholder="Ingrese el Nombre del Predio" autocomplete="off" required />
                        </div>
                        <div class="col-sm-3">
                            <label class="control-label">Guías a generar:</label>
                            <select class="form-control nuevo_cmb prod_cmb" v-model="registroNuevo.guias">
                              <option value=null selected props="disabled" disabled>Selecciona</option>
                              <option value="1">1</option>
                              <option value="2">2</option>
                              <option value="3">3</option>
                              <option value="4">4</option>
                              <option value="5">5</option>
                            </select>
                        </div>
                      </div>
                    </div>

                    <div class="col-sm-12">
                      <div class="form-group">
                        <div class="col-sm-3">
                          <label class="control-label">Estado:</label>
                          <select :disabled="registroNuevo.estatusEdit" class="form-control" v-model="registroNuevo.estado" @change="cargarMunLoc('M')">
                            <option value=null selected props="disabled" disabled>Selecciona un Estado</option>
                            <option v-for="estado in estados" :key="estado.id" :value="estado.id">{{estado.value}} </option>
                          </select>
                        </div>

                        <div class="col-sm-3">
                            <label class="control-label">Municipio:</label>
                            <select :disabled="registroNuevo.estatusEdit || !registroNuevo.estado>0" class="form-control" v-model="registroNuevo.municipio" @change="cargarMunLoc('L')">
                              <option value=null selected props="disabled" disabled>Selecciona un Municipio</option>
                              <option v-for="estado in municipios" :key="estado.id" :value="estado.id">{{estado.value}} </option>
                            </select>
                        </div>
                    
                        <div class="col-sm-3">
                          <label class="control-label">Localidad:</label>
                          <select :disabled="registroNuevo.estatusEdit || (!registroNuevo.estado>0 && !registroNuevo.municipio>0)" class="form-control" v-model="registroNuevo.id_localidad" >
                            <option value=null selected props="disabled" disabled>Selecciona una Localidad</option>
                            <option v-for="estado in localidades" :key="estado.id" :value="estado.id">{{estado.value}} </option>
                          </select>
                        </div>
                        <div class="col-sm-3">
                          <label class="control-label">Superficie(Hectáreas):</label>
                          <input type="number" class='form-control mayus' v-model="registroNuevo.superficie" placeholder="Ingrese Superficie en hectáreas" autocomplete="off" required />
                        </div>
                      
                      </div>
                    </div>

                    <div class="col-sm-12">
                      <div class="form-group">
                        <div class="col-sm-3">
                          <label class="control-label">Tenencia de la Tierra:</label>
                          <select :disabled="registroNuevo.estatusEdit" class="form-control" v-model="registroNuevo.tenencia" >
                            <option value=null selected props="disabled" disabled>Selecciona un Estado</option>
                              <option value="EJIDAL">EJIDAL</option>
                              <option value="COMUNAL">COMUNAL</option>
                              <option value="PRIVADA">PRIVADA</option>
                          </select>
                        </div>

                        <div class="col-sm-3">
                            <label class="control-label">Usufruto de la Tierra:</label>
                            <select :disabled="registroNuevo.estatusEdit" class="form-control" v-model="registroNuevo.usufruto" >
                              <option value=null selected props="disabled" disabled>Selecciona un Municipio</option>
                              <option value="A MEDIAS">A MEDIAS</option>
                              <option value="RENTADO">RENTADO</option>
                              <option value="PROPIEDAD">PROPIEDAD</option>
                              <option value="PRESTADO">PRESTADO</option>
                            </select>
                        </div>

                        <div class="col-sm-3">
                            <label class="control-label">Latitud Norte:</label>
                            <input type="number" class='form-control mayus' v-model="registroNuevo.lat" placeholder="Ingrese la latitud" autocomplete="off" required />
                        </div>

                        <div class="col-sm-3">
                            <label class="control-label">Longitud Oeste:</label>
                            <input type="number" class='form-control mayus' v-model="registroNuevo.lng" placeholder="Ingrese la longitud" autocomplete="off" required />
                        </div>
                      </div>
                    </div>

                    <div class="col-sm-12">
                      <div class="form-group">
                        <h4>Datos de las Plantas</h4>
                        <div class="col-sm-3">
                          <label class="control-label">Registro de Maguey:</label>
                          <select :disabled="registroNuevo.estatusEdit" class="form-control" v-model="registroNuevo.regmaguey" >
                            <option value=null selected props="disabled" disabled>Selecciona un Estado</option>
                            <option value="CULTIVADO">CULTIVADO</option>
                            <option value="SEMICULTIVADO">SEMICULTIVADO</option>
                            <option value="SILVESTRE">SILVESTRE</option>
                          </select>
                        </div>

                        <div class="col-sm-3">
                            <label class="control-label">Distancia(Surcos):</label>
                            <input type="number" class='form-control mayus' v-model="registroNuevo.dis_surcometros" placeholder="Ingrese la distancia en surcos" autocomplete="off" required />
                        </div>
                    
                        <div class="col-sm-3">
                          <label class="control-label">Distancia(Plantas):</label>
                          <input type="number" class='form-control mayus' v-model="registroNuevo.dis_planmetros" placeholder="Ingrese la distancia entre plantas" autocomplete="off" required />
                        </div>
                        <div class="col-sm-3">
                          <label class="control-label">Especie:</label>
                          <input type="text" class='form-control mayus' v-model="registroNuevo.paraje" placeholder="Ingrese la Especie" autocomplete="off" required />
                        </div>
                      </div>
                    </div>

                    <div class="col-sm-12">
                      <div class="form-group">
                        <div class="col-sm-3">
                          <label class="control-label">No. de Plantas:</label>
                          <input type="number" class='form-control mayus' v-model="registroNuevo.cantidadini" placeholder="Ingrese el Número de Plantas" autocomplete="off" required />
                        </div>

                        <div class="col-sm-3">
                            <label class="control-label">Edad:</label>
                            <input type="number" class='form-control mayus' v-model="registroNuevo.edad" placeholder="Ingrese la Edad" autocomplete="off" required />
                        </div>

                        <div class="col-sm-3">
                            
                        </div>
                    
                      </div>
                    </div>

                    <div class="col-sm-12">
                      <div class="form-group">
                        <div class="col-sm-12">
                          <table class="table-mini-font table table-hover" id="predios">
                            <thead style="background: #11324D;color: #F8F1F1;">
                              <tr>
                                <th style="width: 1%; "></th>
                                <th style="width: 6%; "># CONTROL</th>
                                <th style="width: 6%; ">CLIENTE</th>
                                <th style="width: 10%; ">PARAJE</th>
                                <th style="width: 6%; ">LATITUD</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr data-index="0"> 
                                <td style="width: 1%; ">-</td> 
                                <td style="width: 6%; ">P1</td> 
                                <td style="width: 6%; ">C0013</td> 
                                <td style="width: 10%; ">P1</td> 
                                <td style="width: 6%; ">16.862626</td> 
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                </div>
                <div class="tab-pane" id="Guias">
                
                </div>
                <div class="tab-pane active" id="Atributos">
                  
                  <div class="col-sm-12">
                    <div class="form-group" v-for="atributo in atributos" :key="atributo.id"> 
                      <div class="panel  panel-default" style="margin-bottom:0 !important;">
                        <div class="panel-body">
                            <div class="col-sm-12">
                              <div class="col-sm-8">
                                <label class="control-label" style="text-align: left">{{atributo.value}}</label>
                              </div>
                              <div class="col-sm-1">
                                <input class="form-check-input" v-model="atributo.estatus" :id="'paramSwitchA'+atributo.index" type="checkbox" role="switch" id="flexSwitchCheckDefault" @change="cambiarEstatus(atributo)" />
                              </div>
                              <div class="col-sm-3">
                                <input :disabled="!atributo.estatus" class="form-control mayus" type="date" v-model="atributo.fecha" />
                              </div>
                            </div>
                            <br><br>
                            <div class="col-sm-12">
                              <div class="col-sm-4">
                                <img :src="'images/'+atributo.img"  alt="Responsive image" width="120" style="text-align: center" />
                              </div>
                              <div class="col-sm-8">
                                <span>{{atributo.detalles}}</span>
                              </div>
                            </div>
                            <br><br>
                            <div class="col-sm-12">
                              <div class="col-sm-4">
                                <label class="form-label" >ARCHIVOS A IMPORTAR</label>
                                <div class="input-group">
                                  <input type="file" class="form-control" aria-describedby="inputGroupFileAddon04" aria-label="Upload"  multiple 
                                  :name="'ArchivosI'+atributo.id" :id="'ArchivosI'+atributo.id" :disabled="registroNuevo.estatusEdit || !atributo.estatus">
                                </div>
                              </div>
                              <div class="col-sm-8">
                                <label class="form-label" >OBSERVACIONES:</label> 
                                <textarea class="form-control" v-model="atributo.observaciones" :disabled="registroNuevo.estatusEdit || !atributo.estatus"></textarea>
                              </div>
                            </div>
                            <label class="form-label" >ACHIVOS AGREGADOS</label>
                            <div class="col-sm-12">
                              <div class="col-sm-4" v-for="foto in atributo.fotos" :key="foto.id">
                                <div class="input-group" v-if="registroNuevo.estatusEdit || atributo.estatus">
                                  <input class="form-check-input" v-model="foto.estatus" :id="'paramSwitchF'+atributo.index" type="checkbox" role="switch" id="flexSwitchCheckDefault" />&nbsp;
                                  <img :src="'images/'+atributo.img"  alt="Responsive image" width="10px" />&nbsp;
                                  <a target="_blank" :href="'./php/documentos.php?f='+foto.id" :id="'lb_doc_'+foto.id" :name="'lb_doc_'+foto.id" style="font-size: 10px;font-weight: 600;cursor: pointer;color: #0d47a1;">{{foto.nombre}}</a>
                                </div>
                                <div class="input-group" v-else>
                                  <input class="form-check-input" v-model="foto.estatus" :id="'paramSwitchF'+atributo.index" type="checkbox" role="switch" id="flexSwitchCheckDefault" :disabled="registroNuevo.estatusEdit || !atributo.estatus" />&nbsp;
                                  <img style="opacity: 0.5;" :src="'images/'+atributo.img"  alt="Responsive image" width="10px" />&nbsp;
                                  <span style="font-size: 10px;font-weight: 600;color: currentColor; cursor: not-allowed; opacity: 0.5;text-decoration: none;">{{foto.nombre}}</span>
                                </div>
                              </div>
                            </div>
                        </div>
                      </div>
                    </div>
                  </div>

                </div>
            </div>

            
            
          </form>
        </div>
        <div :class="registroNuevo.id > 0 ? 'modal-footer d-flex justify-content-between': 'modal-footer text-end' ">
            <!--<button type="button" v-if="registroNuevo.id > 0 && !registroNuevo.estatusEdit" @click="eliminaRegistro(registroNuevo.id)" class="btn rounded-pill btn-icon btn-danger"><span class="tf-icons bx bx-trash"></span></button>
            <button type="button" :disabled="bloqueaBoton" v-if="!registroNuevo.id || !(registroNuevo.id > 0) " class="btn btn-primary" @click="registraPredio">Agregar</button>-->
            <button type="button" :disabled="bloqueaBoton" v-if="registroNuevo.id > 0 && !registroNuevo.estatusEdit" class="btn btn-primary" @click="registraPredio">Modificar</button>
        </div>
      </div>
    </div>
  </div>
    
  `,
  props: {
    option: Number,
    idregistro: Number,
  },
  setup(props,{ emit }) {
    const { option,idregistro } = toRefs(props);

    const registroNuevo = ref({
      id:0,
      id_paraje:null,
      id_localidad:null,
      id_cliente:null,
      paraje:null,
      lat:null,
      lng:null,
      tenencia:null,
      superficie:null,
      referencia:null,
      usufruto:null,
      fecha:null,
      nombrep:null,
      fecha_paraje:null,
      rcampo:null,
      tipo:null,
      maguey_con_registro:null,
      servicio:null,
      estado:null,
      municipio:null,
      propietario:null,
      regmaguey:null,
      id_comun:null,
      edad:null,
      cantidadini:null,
      dis_planmetros:null,
      dis_surcometros:null,
      regmaguey:null,
      observaciones:null
    });
    
    const resetFormRegistro = () => {
        registroNuevo.value.id = 0;
        registroNuevo.value.id_paraje = null;
        registroNuevo.value.id_localidad = null;
        registroNuevo.value.id_cliente = null;
        registroNuevo.value.paraje = null;
        registroNuevo.value.lat = null;
        registroNuevo.value.lng = null;
        registroNuevo.value.tenencia = null;
        registroNuevo.value.superficie = null;
        registroNuevo.value.referencia = null;
        registroNuevo.value.usufruto = null;
        registroNuevo.value.fecha = null;
        registroNuevo.value.nombrep = null;
        registroNuevo.value.fecha_paraje = null;
        registroNuevo.value.rcampo = null;
        registroNuevo.value.tipo = null;
        registroNuevo.value.maguey_con_registro = null;
        registroNuevo.value.servicio = null;
        registroNuevo.value.estado = null;
        registroNuevo.value.municipio = null;
        registroNuevo.value.propietario = null;
        registroNuevo.value.observaciones = null;
    }
    const clienteSelected = ref(null);
    const variableAtributo = ref(null);
    const bloqueaBoton = ref(false);

    const submitForm = (event) => {
      event.preventDefault();
    }

    const validarRFC = (event) => {
      if (registroNuevo.value.rfc.length === 10 && registroNuevo.value.rfc.toUpperCase() === "XXXXXXXXXX") {
        registroNuevo.value.generico = 1;
        registroNuevo.value.calle = "NO INDICA";
        registroNuevo.value.no_exterior = "NO INDICA";
        registroNuevo.value.colonia = "NO INDICA";
        registroNuevo.value.localidad = "NO INDICA";
        registroNuevo.value.municipio = "NO INDICA";
        registroNuevo.value.codigo_postal = "NO INDICA";
        registroNuevo.value.Estado = "NO INDICA";
        registroNuevo.value.nombre_comercial = "NO INDICA";
        registroNuevo.value.moneda = "999";
        registroNuevo.value.regimen_fiscal = "999";
        registroNuevo.value.contacto = "NO INDICA";
        registroNuevo.value.telefono_contacto = "NO INDICA";
      } else {
        registroNuevo.value.generico = null;
      }
    }

    const cambiarEstatus = (selAtributo) => {
      variableAtributo.value = selAtributo.id;
      //indSel = selAtributo
      let contAct = 0;
      if(selAtributo.id === 4 && atributos.value[selAtributo.id-1].estatus){
        //console.log("entró 4");
        atributos.value.forEach((element, index) => {
          if(element.id !== 4){
            //console.log("no 4: es "+element.id);
            if(element.estatus) {
              contAct++;
            }
          }
        });
        if(contAct > 0){
          //selAtributo.estatus = true;
          //console.log("Si tiene estatus activo");
          Swal.fire({
            title: `Confirmar activación de Atributo`,
            text: `Si se activa este atributo, los otros deben ser desactivados, ¿Está seguro de activarlo?`,
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Confirmar",
            cancelButtonText: "Cancelar",
          }).then((result) => {
            if (result.isConfirmed) {
              //selAtributo.estatus = true;
              atributos.value.forEach((element, index) => {
                if(element.id !== 4){
                  element.estatus = false;
                }
              });
            } else {
              atributos.value[selAtributo.id-1].estatus = false;
            }
          });
        }
      } else {
        if(atributos.value[3].estatus) {
          Swal.fire({
            title: `Confirmar activación de Atributo`,
            text: `Se desactivará el atributo "Conservación de Maguey Silvestre", ¿Está seguro de continuar?`,
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Confirmar",
            cancelButtonText: "Cancelar",
          }).then((result) => {
            if (result.isConfirmed) {
              atributos.value[3].estatus = false;
            } else {
              atributos.value[selAtributo.id-1].estatus = false;
            }
          });
        }
      }
      //console.log(atributos.value[selAtributo.id-1].estatus);
    }

    watch(variableAtributo, async (newValue, oldValue) => {
      //console.log(newValue + " : " + oldValue);
      //console.log(atributos.value[newValue-1].estatus);
      /*if(atributos.value[newValue-1].estatus && newValue === 4){
        console.log("entró");
      }*/
      /*if(newValue === 4){
        console.log("entro 4");
        //
      }*/
      /*console.log(atributos.value[selAtributo.id-1].estatus);
      if(atributos.value[selAtributo.id-1].estatus && selAtributo.id === 4){
        console.log("entró");
      }*/
    });

    const cargarMunLoc = (tipo) => {
      if (registroNuevo.value.estado > 0 && tipo === "M") {
        registroNuevo.value.municipio = null;
        registroNuevo.value.id_localidad = null;
        axios({
          method: "get",
          url: "php/index.php",
          params: {
              action: "getMunicipios",
              estado: registroNuevo.value.estado,
        },
        })
        .then(({ status, data }) => {
            if (status === 200) {
              municipios.value = JSON.parse(data.municipios);
            }
        })
        .catch((error) => {
            console.log(error);
        });
      } else if (registroNuevo.value.municipio > 0 && tipo === "L") {
        registroNuevo.value.id_localidad = null;
        axios({
          method: "get",
          url: "php/index.php",
          params: {
              action: "getLocalidades",
              municipio: registroNuevo.value.municipio,
        },
        })
        .then(({ status, data }) => {
            if (status === 200) {
              localidades.value = JSON.parse(data.localidades);
            }
        })
        .catch((error) => {
            console.log(error);
        });
      }
    }
    const registraPredio = () => {
      let atotalel = 0;
      let afaltantes = 0;
      let faltantes = 0;
      var formData = new FormData();
      atributos.value.forEach((element, index) => {
        let inputFile = [];
        if(element.estatus){
          inputFile = [];
          inputFile = document.querySelector("#ArchivosI" + element.id);
          if (inputFile.files.length > 0) {
              for (const archivo of inputFile.files) {
                formData.append("documentos"+element.id+"[]", archivo);
              }
          } else {
              formData.append('documentos'+element.id+'[]', '');
          }
          if(element.fecha === ""){
            faltantes++;
          }
        } else {
          afaltantes++;
        }
        atotalel++;
      });
      //console.log(afaltantes + " : " + atotalel);
      if(afaltantes === atotalel){
        Swal.fire({
          title: "Importante!",  
          text: "Se debe activar por lo menos un atributo.",
          icon: "warning",
          confirmButtonText: "Ok",
        });
        return;
      }
      if(faltantes > 0){
        Swal.fire({
          title: "Importante!",  
          text: "Si se activa algún atributo se debe de seleccionar la fecha.",
          icon: "warning",
          confirmButtonText: "Ok",
        });
        return;
      }      

      formData.append('registro',  JSON.stringify(registroNuevo.value));
      formData.append('atributos',  JSON.stringify(atributos.value));
      formData.append('usuario',    id_usuario);
      formData.append('action',     "registraPredio");

      const titletxt1 = (registroNuevo.value.id > 0) ? "Modificación": "Registro";
      const titletxt2 = (registroNuevo.value.id > 0) ? "modificar": "registrar";
      const titletxt3 = (registroNuevo.value.id > 0) ? "modificado": "registrado";
      Swal.fire({
        title: `Confirmar ${titletxt1} de Predio`,
        text: `¿Estás seguro de que deseas ${titletxt2} a este Predio?`,
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Confirmar",
        cancelButtonText: "Cancelar",
      }).then((result) => {
        if (result.isConfirmed) {
          bloqueaBoton.value = true;
          $.blockUI({ 
              message: '<h1>Espere un momento por favor!</h1>',
              css: { 
              border: 'none', 
              padding: '15px', 
              backgroundColor: '#000', 
              '-webkit-border-radius': '10px', 
              '-moz-border-radius': '10px', 
              opacity: .5, 
              color: '#fff',
              baseZ: 20000,
              zIndex: 20000,
          } }); 
          axios
            .post("php/index.php", formData, {
              headers: {
                'Content-Type': 'multipart/form-data'
              },
            })
            .then(({ data, status }) => {
              const { codigo,msg,cliente } = data;
              setTimeout($.unblockUI, 100); 
              bloqueaBoton.value = false;
              if (status === 200 && codigo === 0) {
                Swal.fire({
                  title: `Predio ${titletxt3}`,
                  text: `El Predio ha sido ${titletxt3} con éxito.`,
                  icon: "success",
                  confirmButtonText: "Ok",
                });
                modalRegistro.value.hide();
                emit('cierra');
                resetFormRegistro();
              }else{
                Swal.fire({
                  title: "Importante!",
                  text: msg,
                  icon: "warning",
                  confirmButtonText: "Ok",
                });
              }
            })
            .catch((error) => {
              console.log(error);
              Swal.fire("Registro Cancelado", "No se ha registrado el Predio.", "error");
            });
        }
      });
    }


    const closeModal = () => {
      modalRegistro.value.hide();
      emit('cierra');
    }

    const eliminaCliente = (idC) => {
      Swal.fire({
        title: "Confirmar Eliminación del Cliente",
        html: `
          <div>
            <p>¿Estás seguro de que deseas eliminar este Cliente? Esta acción es irreversible.</p>
          </div>
        `,
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Confirmar",
        cancelButtonText: "Cancelar",
        allowOutsideClick: () => !Swal.isLoading(), // Evita cerrar al hacer clic fuera de la alerta durante la carga
        
      }).then((result) => {
        if (result.isConfirmed) {
          var formData = new FormData();
          formData.append('action',     "eliminaCliente");
          formData.append('usuario',     username_id);
          formData.append('clienteId',     idC);

          axios
            .post("recursos/php/clientes/index.php", formData, {
              headers: {
                "Content-Type": "application/x-www-form-urlencoded",
              },
            })
            .then(({ data, status }) => {
              Swal.fire(
                "Operación Realizada",
                "El Cliente se ha eliminado.",
                "success"
              );
              modalRegistro.value.hide();
              emit('cierra');
              resetFormRegistro();
            })
            .catch((error) => {
              Swal.fire(
                "Operación Cancelada",
                "No se ha podido completar la operación. Por favor, inténtalo de nuevo más tarde.",
                "error"
              );
            })
            .finally(() => {});
        }
      });
    }
    const atributos = ref({});
    const estados = ref({});
    const municipios = ref({});
    const localidades = ref({});
    const batributos = ref({});
    const opciones = () => {
        axios({
        method: "get",
        url: "php/index.php",
        params: {
            action: "getOpciones",
        },
        })
        .then(({ status, data }) => {
            if (status === 200) {
              estados.value = JSON.parse(data.estados);
              atributos.value = data.atributos;
              //console.log(data);
            }
        })
        .catch((error) => {
            console.log(error);
        });
    }

    const modalRegistro = ref(null);
    onMounted(async() => {
      $(`#id_cliente`)
        .autocomplete({
          source: function (request, response) {
            var texto = request.term;
            $.ajax({
              url: "php/index.php",
              dataType: "json",
              data: {
                action: "suggest",
                term: texto,
              },
              success: function (data) {
                response(data);
                return false;
              },
            });
          },
          minLength: 1,
          maxRows: 15,
          select: function (e, ui) {
            console.log("cambio1");
            //mueSaldosMembresia.value = true;
            if (registroNuevo.value?.id_cliente !== ui.item?.id) {
              clienteSelected.value = ui.item;
            } else {
              clienteSelected.value = ui.item;
            }
            registroNuevo.value.id_cliente = ui.item.id;
            registroNuevo.value.nombre = ui.item.nombre;
          },
          change: function (event, ui) {
            console.log("cambio2");
            /*if (!ui.item && clienteSelected.value) {
              $(`#inputEmpresaFact`).val(nuevoRegistro.value.value);
            }*/
          },
        })
        .keypress(function (e) {
          if (e.keyCode === 13) {
            return false;
          }
        });
      $("#id_cliente").autocomplete( "option", "appendTo", ".modal-body" );
      opciones();
      
      
      
      resetFormRegistro();
      //console.log("idregistro:" + idregistro.value);
      modalRegistro.value = new bootstrap.Modal(document.getElementById("modalContinua"));
      if(idregistro.value > 0) {
          try {
            const { data } = await axios.get(
              "php/index.php", {
                params: {
                  action: "getDatosPredio",
                  id: idregistro.value,
                },
              });
            if (data) {
              registroNuevo.value = JSON.parse(data.datosPredio);
              batributos.value = JSON.parse(data.datosAtributos);
              batributos.value.forEach((belement, bindex) => {
                atributos.value.forEach((element, index) => {
                  if(belement.id === element.id ){
                    element.estatus = true;
                    element.fecha = belement.fecha;
                    element.observaciones = belement.observaciones;
                    element.fotos = belement.fotos;
                    element.id_paa = belement.id_paa;
                  }
                });
              });
              //console.log(atributos.value);
            }
          } catch (error) {
            if (error.response?.data?.msg) {
              alert(error.response?.data?.msg);
            }
          }
      }
      nextTick(() => {
        modalRegistro.value.show();
      });
      
      
    });

    

    return { 
      submitForm,
      modalRegistro,
      registroNuevo,
      eliminaCliente,
      registraPredio,
      closeModal,
      idregistro,
      validarRFC,
      estados,
      municipios,
      localidades,
      atributos,
      opciones,
      cargarMunLoc,
      cambiarEstatus,
      bloqueaBoton
    };
  }
});


export default ClientesForm
