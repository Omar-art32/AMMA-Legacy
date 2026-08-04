const { createApp, ref, computed, onMounted, watch,onUnmounted } = Vue;
const app = createApp({
  setup() {

    const nuevoRegistro = ref(false);
    const idEC = ref(null);
    const listproducts = ref([]);
    const noControl = ref(null);
    const nomEmpresa = ref(null);

    const filters = ref({
      no_control:null,
      texto:null,
      tiporegistro: null,
      atributo: null
    })

    function cellStyle(value, row, index, field) {
      if (row.atributos !== "" && row.atributos !== null) {
        return {classes: 'terminada'};
      }
      else {
        return {};
      }
    }
    const getAllRows = (id, action) => {
        $(id).bootstrapTable("destroy");
        $(id).bootstrapTable({
          url: "php/index.php",
          method: "GET",
          contentType: "application/x-www-form-urlencoded;charset=UTF-8",
          queryParams: function (p) {
            return {
              action: action,
              limit: p.limit,
              offset: p.offset,
              no_control: filters.value.no_control,
              texto: filters.value.texto,
              tipo_registro: filters.value.tiporegistro,
              atributo: filters.value.atributo,
            };
          },
          columns: [
            {
              title: "",
              width: "1%",
              cellStyle: cellStyle
            }, {
              field: "id_paraje",
              title: "PARAJE",
              width: "2%"
            }, {
              field: "paraje",
              title: "PARAJE",
              width: "4%"
            }, {
              field: "id_cliente",
              title: "CLIENTE",
              width: "3%"
            }, {
              field: "nombre_cliente",
              title: "NOMBRE DE CLIENTE",
              width: "8%"
            }, {
              field: "nombrep",
              title: "PRODUCTOR",
              width: "8%"
            },{
              field: "rcampo",
              title: "REPRESENTANTE<BR>EN CAMPO",
              width: "8%"
            },{
              field: "superficie",
              title: "SUPERFICIE",
              width: "2%"
            },/* {
              field: "lat",
              title: "LATITUD",
              width: "3%"
            }, {
              field: "lng",
              title: "LONGITUD",
              width: "3%"
            }, */{
              field: "localidad",
              title: "LOCALIDAD",
              width: "4%"
            }, {
              field: "municipio",
              title: "MUNICIPIO",
              width: "4%"
            }, {
              field: "estado",
              title: "ESTADO",
              width: "4%"
            }, {
              field: "guias_veces",
              title: "GUÍAS",
              width: "4%"
            }, {
              field: "registro",
              title: "REGISTRO",
              width: "4%"
            }, {
              field: "origen",
              title: "ORIGEN",
              width: "4%"
            }, {
              field: "",
              title: "",
              width: "4%",
              formatter: operateFormatter
            }
          ],

          sortStable: true,
          sortOrder: "desc",
          pageNumber: 1, // pagina q se muestra por default
          pageSize: 10,
          pageList: [10, 25, 50, 100], //
          smartDisplay: true,
          sidePagination: "server",
          paginationVAlign: "bottom", //formato de botones en paginacion
          cache: false,
          maintainSelected: true,
          pagination: true,
          showRefresh: false,
        });
        $(id)
          .on("all.bs.table", function (e, name, args) {})
          .on("dbl-click-row.bs.table", function (e, row, $element) {})
          .on("dbl-click-row.bs.table", function (e, row, $element) {
            abreRegistro(row.id);
          });
    };
    function operateFormatter(value, row, index) {
        //const compDM = (row.atributos != "" && row.atributos != null) ? '&nbsp;<a class="pdf2" href="php/descargar_documento.php?id='+row.mdNA+'&tipo=D&nc='+row.cliente+'" title="Constancia" target="_blank"><span style="font-size: 1em; color: Green;"><i class="fa fa-file-pdf-o fa-lg" aria-hidden="true"></i></span></a>': "";
        // <a onClick="muestraInformes('+row.folio+')" style="text-decoration: none;cursor: pointer;color: #157b5f;font-weight: 700;"><img src="images/iconos/carpeta.svg" height="30" style="cursor: pointer;" alt=""/><br><small>MOSTRAR INFORME(S)</small></a>
        const compDM = (row.atributos != "" && row.atributos != null) ? '&nbsp;<a onClick="imprime('+row.id+')" style="text-decoration: none;cursor: pointer;color: #157b5f;font-weight: 700;"><span style="font-size: 1em; color: Green;" title="Constancia"><i class="fa fa-file-pdf-o fa-lg" aria-hidden="true"></i></span></a>': "";
        return [
              compDM
        ].join('');

    }

    app.config.globalProperties.imprime = (id) => {
      window.open('php/reporte_formato_puebla_v2.php?id='+id);
    };
    const idRegistro = ref(null);

    const abreRegistro = (value) => {
      //console.log("holiis");
      idRegistro.value = value;
      nuevoRegistro.value = true;
    }

    const actualizaRegistros = () => {
      idRegistro.value = null;
      nuevoRegistro.value = false;
      $("#predios").bootstrapTable("refresh");
  }

    const restart = () => {
      idRegistro.value = null;
      nuevoRegistro.value = false;
      filters.value.no_control = null;
      filters.value.texto = null;
      filters.value.tiporegistro = null;
      filters.value.atributo = null;
      $("#predios").bootstrapTable("refresh");
    }

    app.config.globalProperties.abrePublicacion = (value) => {
      //console.log("abrePublicacion");
      idEC.value = value;
      nuevoRegistro.value = true;
      //console.log(nuevoRegistro.value);
    };

    window.abrePublicacion = app.config.globalProperties.abrePublicacion;
    window.imprime = app.config.globalProperties.imprime;

    const gatributos = ref({});
    
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
              gatributos.value = data.atributos;
            }
        })
        .catch((error) => {
            console.log(error);
        });
    }
  
    onMounted(() => {
      $(`#fil_control`)
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
          filters.value.no_control = ui.item.id;
          if (filters.value.no_control !== ui.item?.id) {
            filters.value.no_control = ui.item.id_cliente;
            filters.value.nombre = ui.item.nombre;
          } else {
            //clienteSelected.value = ui.item;
            filters.value.no_control = ui.item.id_cliente;
            filters.value.nombre = ui.item.nombre
          }
          filters.value.no_control = ui.item.id;
          filters.value.nombre = ui.item.nombre
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
        getAllRows("#predios", "predios");
        opciones();
    });
    
    return {
        nuevoRegistro,
        listproducts,
        restart,
        idEC,
        idRegistro,
        actualizaRegistros,
        noControl,
        nomEmpresa,
        filters,
        opciones, 
        gatributos
    };
  },
});

// import modalFormRegistro from "./FormRegistro.js?0000000000";
import modalFormRegistro from "./FormRegistroP.js?0000000";

app.component("modal-registro", modalFormRegistro);


app.mount("#wrapper");
