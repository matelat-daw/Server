// ========================================
// SISTEMA DE PAGINACIÓN OPTIMIZADO
// ========================================

// Objeto para gestionar el estado de paginación
const PaginationManager = {
    currentPage: 1,
    itemsPerPage: 5,
    totalItems: 0,
    dataSource: null,
    containerSelector: '#table',
    
    // Inicializar la paginación
    init: function(dataSource, itemsPerPage = 5) {
        this.dataSource = dataSource;
        this.itemsPerPage = itemsPerPage;
        this.totalItems = dataSource.length;
        this.currentPage = 1;
        this.render();
    },
    
    // Calcular total de páginas
    getTotalPages: function() {
        return Math.ceil(this.totalItems / this.itemsPerPage);
    },
    
    // Ir a página anterior
    prevPage: function() {
        if (this.currentPage > 1) {
            this.currentPage--;
            this.render();
        }
    },
    
    // Ir a página siguiente
    nextPage: function() {
        if (this.currentPage < this.getTotalPages()) {
            this.currentPage++;
            this.render();
        }
    },
    
    // Ir a una página específica
    goToPage: function(pageNumber) {
        const totalPages = this.getTotalPages();
        if (pageNumber >= 1 && pageNumber <= totalPages) {
            this.currentPage = pageNumber;
            this.render();
        }
    },
    
    // Obtener items de la página actual
    getCurrentPageItems: function() {
        const startIndex = (this.currentPage - 1) * this.itemsPerPage;
        const endIndex = startIndex + this.itemsPerPage;
        return this.dataSource.slice(startIndex, endIndex);
    },
    
    // Renderizar tabla y controles de paginación
    render: function() {
        this.renderTable();
        this.updateControls();
    },
    
    // Renderizar tabla según tipo de datos
    renderTable: function() {
        const container = document.getElementById('table');
        if (!container) return;
        
        const items = this.getCurrentPageItems();
        
        // Detectar tipo de datos y renderizar apropiadamente
        if (this.dataSource.isInvoice) {
            container.innerHTML = this.renderInvoiceTable(items);
        } else {
            container.innerHTML = this.renderServiceTable(items);
        }
    },
    
    // Renderizar tabla de servicios (index)
    renderServiceTable: function(items) {
        const rows = items.map(item => {
            // Ajustar ruta de imagen
            let imgPath = item.img;
            if (imgPath.indexOf('img/') === 0) {
                imgPath = imgPath.replace('img/', 'assets/img/');
            }
            if (imgPath.indexOf('/') !== 0 && imgPath.indexOf('http') !== 0) {
                imgPath = '/Barbery/' + imgPath;
            }
            
            return `
                <tr>
                    <td><strong>${item.service}</strong></td>
                    <td class="text-end"><strong>${item.price} $</strong></td>
                    <td class="text-center">
                        <a href="javascript:void(0);" onclick="showImg('${imgPath}')">
                            <img src="${imgPath}" 
                                 alt="${item.service}" 
                                 class="service-thumbnail"
                                 loading="lazy">
                        </a>
                    </td>
                </tr>
            `;
        }).join('');
        
        return `
            <table class="table table-hover table-modern">
                <thead>
                    <tr>
                        <th>Servicio</th>
                        <th class="text-end">Precio</th>
                        <th class="text-center">Foto</th>
                    </tr>
                </thead>
                <tbody>
                    ${rows || '<tr><td colspan="3" class="text-center">No hay servicios disponibles</td></tr>'}
                </tbody>
            </table>
        `;
    },
    
    // Renderizar tabla de facturas (profile)
    renderInvoiceTable: function(items) {
        const rows = items.map(item => {
            const dateFormatted = item.date.split('-').reverse().join('/');
            const servicesHtml = item.services.map(s => `<div>${s}</div>`).join('');
            const pricesHtml = item.prices.map(p => `<div class="text-end">${p} $</div>`).join('');
            const qttiesHtml = item.quantities.map(q => `<div class="text-end">${q}</div>`).join('');
            const partialsHtml = item.services.map((s, idx) => 
                `<div class="text-end">${(parseFloat(item.prices[idx]) * parseInt(item.quantities[idx])).toFixed(2)} $</div>`
            ).join('');
            
            return `
                <tr>
                    <td>${item.invoice}</td>
                    <td>${servicesHtml}</td>
                    <td>${pricesHtml}</td>
                    <td>${qttiesHtml}</td>
                    <td>${partialsHtml}</td>
                    <td class="text-end"><strong>${item.total} $</strong></td>
                    <td>${dateFormatted}</td>
                    <td>${item.time}</td>
                </tr>
            `;
        }).join('');
        
        return `
            <table class="table table-hover table-modern">
                <thead>
                    <tr>
                        <th>N° Factura</th>
                        <th>Servicio</th>
                        <th class="text-end">Precio</th>
                        <th class="text-end">Cantidad</th>
                        <th class="text-end">Parcial IVA Inc.</th>
                        <th class="text-end">Total</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                    </tr>
                </thead>
                <tbody>
                    ${rows || '<tr><td colspan="8" class="text-center">No hay facturas disponibles</td></tr>'}
                </tbody>
            </table>
        `;
    },
    
    // Actualizar controles de paginación
    updateControls: function() {
        const pageSpan = document.getElementById('page');
        const btnPrev = document.getElementById('prev');
        const btnNext = document.getElementById('next');
        
        if (!pageSpan || !btnPrev || !btnNext) return;
        
        const totalPages = this.getTotalPages();
        
        // Mostrar u ocultar controles según cantidad de items
        if (this.totalItems <= this.itemsPerPage) {
            pageSpan.style.display = 'none';
            btnPrev.style.visibility = 'hidden';
            btnNext.style.visibility = 'hidden';
            return;
        }
        
        // Mostrar información de página
        pageSpan.style.display = 'inline';
        pageSpan.innerHTML = `Página <strong>${this.currentPage}</strong> de <strong>${totalPages}</strong> 
                             <span class="text-muted">(${this.totalItems} resultados)</span>`;
        
        // Controlar visibilidad de botones
        btnPrev.style.visibility = this.currentPage > 1 ? 'visible' : 'hidden';
        btnNext.style.visibility = this.currentPage < totalPages ? 'visible' : 'hidden';
    }
};

// Funciones legacy para compatibilidad con código existente
function prev(where) {
    PaginationManager.prevPage();
}

function next(where) {
    PaginationManager.nextPage();
}

function change(page, qtty, index) {
    // Convertir datos del formato antiguo al nuevo
    if (index && typeof service !== 'undefined') {
        const servicesData = service.map((s, i) => ({
            service: s,
            price: price[i],
            img: img[i]
        }));
        servicesData.isInvoice = false;
        PaginationManager.init(servicesData, qtty);
        PaginationManager.goToPage(page);
    } else if (!index && typeof invoice !== 'undefined') {
        const invoicesData = invoice.map((inv, i) => ({
            invoice: inv,
            services: service[i],
            prices: price[i],
            quantities: qtties[i],
            total: total[i],
            date: date[i],
            time: time[i]
        }));
        invoicesData.isInvoice = true;
        PaginationManager.init(invoicesData, qtty);
        PaginationManager.goToPage(page);
    }
}

function totNumPages() {
    return PaginationManager.getTotalPages();
}

function toast(warn, ttl, msg) // Función para mostrar el Dialogo con los mensajes de alerta, recibe, Código, Título y Mensaje.
{
    var alerta = document.getElementById("alerta"); // La ID del botón del dialogo.
    var title = document.getElementById("title"); // Asigno a la variable title el h4 con id title.
    var message = document.getElementById("message"); // Asigno a la variable message el h5 con id message;
    var modalHeader = document.querySelector(".modern-modal-header"); // Header del modal
    var modalIcon = document.getElementById("modalIcon"); // Icono del modal
    var modalIconContainer = document.getElementById("modalIconContainer"); // Contenedor del icono
    
    // Limpiar clases previas
    modalHeader.classList.remove("header-success", "header-warning", "header-error");
    
    if (warn == 1) // Si el código es 1, es una alerta/warning.
    {
        modalHeader.classList.add("header-warning");
        modalIcon.className = "fas fa-exclamation-triangle modal-icon";
        title.style.backgroundColor = "#fff3cd"; // Fondo amarillo claro
        title.style.color = "#856404"; // Texto amarillo oscuro
        title.style.border = "2px solid #ffc107";
    }
    else if (warn == 0) // Si no, si el código es 0 es un mensaje satisfactorio.
    {
        modalHeader.classList.add("header-success");
        modalIcon.className = "fas fa-check-circle modal-icon";
        title.style.backgroundColor = "#d1ecf1"; // Fondo azul claro
        title.style.color = "#0c5460"; // Texto azul oscuro
        title.style.border = "2px solid #17a2b8";
    }
    else // Si no, viene un 2, es una alerta de error.
    {
        modalHeader.classList.add("header-error");
        modalIcon.className = "fas fa-times-circle modal-icon";
        title.style.backgroundColor = "#f8d7da"; // Fondo rojo claro
        title.style.color = "#721c24"; // Texto rojo oscuro
        title.style.border = "2px solid #dc3545";
    }
    
    title.innerHTML = ttl; // Muestro el Título del dialogo.
    message.innerHTML = msg; // Muestro los mensajes en el diálogo.
    alerta.click(); // Lo hago aparecer pulsando el botón con ID alerta.
}

function screenSize() // Función para dar el tamaño máximo de la pantalla a las vistas.
{
    let view1 = document.getElementById("view1"); // view1 es la ID del div view1.
    let view2 = document.getElementById("view2");
    let view3 = document.getElementById("view3");
    let view4 = document.getElementById("view4");
    let height = window.innerHeight; // window.innerHeight es el tamaño vertical de la pantalla.

    if (view1.offsetHeight < height) // Si el tamaño vertical de la vista es menor que el tamaño vertical de la pantalla.
    {
        view1.style.height = height + "px"; // Asigna a la vista el tamaño vertical de la pantalla.
    }

    if (view2 != null) // Si existe el div view2
    {
        if (view2.offsetHeight < height)
        {
            view2.style.height = height + "px";
        }
        if (view3 != null)
        {
            if (view3.offsetHeight < height)
            {
                view3.style.height = height + "px";
            }
            if (view4 != null)
            {
                if (view4.offsetHeight < height)
                {
                    view4.style.height = height + "px";
                }
            }
            
        }
    }
}

function verify(event) // Función para validar las contraseñas de registro de alumnos y las de modificación, también valida el D.N.I.
{
    var pass = document.getElementById("pass1"); // pass es la ID del input pass0.
    var pass2 = document.getElementById("pass2"); // pass2 es la ID del input pass1.

    if (pass.value != pass2.value) // Verifico si los valores en los input pass y pass2 no coinciden.
    {
        if (event) {
            event.preventDefault(); // Prevenir el envío del formulario
            event.stopPropagation(); // Detener la propagación del evento
        }
        toast(1, "Hay un Error", "Las contraseñas no coinciden, has escrito: " + pass.value + " y " + pass2.value); // Si no coinciden muestro error.
        return false; // Devuelvo false, el formulario no se envía.
    }
    else // Si son iguales.
    {
        return true; // Devuelvo true, envía el formulario.
    }
}

// Mostrar formulario de registro
function showRegisterForm() {
    document.getElementById('loginContainer').style.display = 'none';
    document.getElementById('registerContainer').style.display = 'block';
}

// Mostrar formulario de login
function showLoginForm() {
    document.getElementById('registerContainer').style.display = 'none';
    document.getElementById('loginContainer').style.display = 'block';
}

// Event listener para el formulario de registro
document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('registerForm');
    
    if (registerForm) {
        registerForm.addEventListener('submit', function(event) {
            const pass1 = document.getElementById("pass1");
            const pass2 = document.getElementById("pass2");
            
            if (pass1.value !== pass2.value) {
                event.preventDefault(); // Prevenir el envío
                event.stopPropagation(); // Detener propagación
                toast(1, "Hay un Error", "Las contraseñas no coinciden, has escrito: " + pass1.value + " y " + pass2.value);
                
                // Limpiar solo los campos de contraseña y enfocar
                pass1.value = '';
                pass2.value = '';
                pass1.focus();
                
                // El formulario permanece visible en la página
                return false;
            }
        });
    }
});

function showEye(which) // Función para mostrar el ojo de los input de las contraseñas, recibe el número del elemento que contiene el ojo.
{
    let eye = document.getElementById("togglePassword" + which); // Asigno a eye la id del elemento que contiene el ojo.
    eye.style.visibility = "visible"; // Hago visible el elemento, el ojo.
}

function spy(which) // Función para el ojito de las Contraseñas al hacer click en el ojito, recibe el número de la ID del input de la password.
{
    const togglePassword = document.querySelector('#togglePassword' + which); // Asigno a la constante togglePassword el input con ID togglePassword + which.
    const password = document.querySelector('#pass' + which); // Asigno a password la ID del input con ID pass + which.
    
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password'; // Asigno a type el resultado de un operador ternario, si presiono el ojito y el tipo del input es password
    // lo cambia a text, si es text lo cambia a password.
    password.setAttribute('type', type); // Le asigno el atributo al input password.
    togglePassword.classList.toggle('fa-eye-slash'); // Cambia el aspecto del ojito, al cambiar el input a tipo texto, el ojo aparece con una raya.
}

function showImg(src) // Not in Use but a Good One
{
    var alertaImg = document.getElementById("alertaImg"); // La ID del botón del dialogo.
    var img = document.getElementById("show_pic"); // Asigno a la variable title el h4 con id title.
        
    img.src = src; // Muestro los mensajes en el diálogo.
    alertaImg.click(); // Lo hago aparecer pulsando el botón con ID alerta.
}

function changeit() // Función para la página de contacto.
{
    var button = document.getElementById("change"); // En la variable button obtengo la ID del input type submit change.
    var contact = document.getElementById("contact"); // En la variable contact obtengo el id del selector.
    var phone = document.getElementById("phone");
    var email = document.getElementById("email");
    var ph = document.getElementById("ph");
    var em = document.getElementById("em");

    if (contact.value != "") // Si el valor en el selector ha cambiado.
    {
        switch (contact.value) // Hago un switch al valor en el selector.
        {
            case "Teléfono":
                email.style.visibility = "hidden";
                phone.style.visibility = "visible";
                em.required = false;
                ph.required = true;
                button.value = "Llamame!";
                break;
            case "Whatsapp":
                email.style.visibility = "hidden";
                phone.style.visibility = "visible";
                em.required = false;
                ph.required = true;
                button.value = "Mandame un Guasap";
                break;
            default:
                email.style.visibility = "visible";
                phone.style.visibility = "hidden";
                ph.required = false;
                ph.value = 1;
                em.required = true;
                button.value = "Espero tu E-mail";
                break;
        }
    }
}

function connect(how)
{
    let mssg = document.getElementById('mssg').value;
    let num = 5492234557972;
    var win = window.open('https://wa.me/' + num + '?text=Por Favor contactame por: ' + how + ' al: ' + mssg + ' Mi nombre es: ', '_blank');
}

function screen() // Esta función comprueba si el ancho de la pantalla es de Ordenador o de Teléfono.
{
    let mobile = document.getElementById("mobile");
    let pc = document.getElementById("pc");
    let width = innerWidth;
    if (width < 965) // Si el ancho es inferior a 965.
    {
        pc.style.visibility = "hidden"; // Oculta el menú de Ordenador
        mobile.style.visibility = "visible"; // Muestra el menú de Teléfono.
    }
    else // Si es mayor o igual a 965;
    {
        pc.style.visibility = "visible"; // Muestra el menú para Ordenador
        mobile.style.visibility = "hidden"; // Oculta el menú para Teléfono.
    }
}

function goThere() // Cuando cambia el selector del menú para Teléfono.
{
    var change = document.getElementById("change").value; // Change obtiene el valor en el selector.
    switch(change)
    {
        case "contact":
            window.open("/Barbery/app/contact.php", "_blank");
        break;
        case "request":
            window.open("/Barbery/app/client/appointments/request.php", "_self");
        break;
        case "profile" :
            window.open("/Barbery/app/client/profile.php", "_self");
        break;
        case "view3" :
            window.open("/Barbery/#view3", "_self");
        break;
        case "view2" :
            window.open("/Barbery/#view2", "_self");
        break;
        default :
            window.open("/Barbery/#view1", "_self");
        break;
    }
}

var valor = "";
var valor2 = "";

function add_service()
{
	var service = document.getElementById("service");
	var qtty = document.getElementById("qtty");
	var invoice = document.getElementById("invoice");
	var show = document.getElementById("servic");
	var factura = document.getElementById("factura");
	
	if (service.value !== "")
	{
		factura.style.visibility = "visible";
		valor += service.value + "," + qtty.value + ",";
		valor2 += service.value + " Cantidad: " + qtty.value + ", ";
		show.innerHTML = valor2;
		service.value = "";
		qtty.value = "1";
		invoice.value = valor;
	}
	else
	{
		toast(1, "No has Selecionado Servicio", "Antes de Tocar el Botón Agregar Servicio, Asegúrate de Seleccionar un Servicio y la Cantidad de los Desplegables.");
	}
}

function printIt(number)
{
    if (number != -1) // Si el numero que llega es distinto de -1.
    {
        var img = document.getElementById("img" + number); // Asigno a la variable img la ID del elemento img + numero de factura.
    }
    else // Si llega -1.
    {
        var img = document.getElementById("img0"); // Estoy viedo la última factura, es la imagen 0, Asigno a la variable img la ID del elemento img0.
    }
    const src = img.src; // Asigno a la constante src la imagen.
    const win = window.open(''); // Asigno a la constante win una nueva ventana abierta.
    win.document.write('<img src="' + src + '" onload="window.print(); window.close();">'); // Escribo en la ventana abierta un elemento img con la imagen a imprimir y la envía a la impresora y al terminar cierra la ventana.
}

function capture(number)
{
    const print = document.getElementById("printable" + number); // Asigna a printi el Div con ID printable + number
    const image = document.getElementById("image" + number); // Asigna a image el Div con ID image + number, contendrá el elemento img con la factura.

    html2canvas(print).then((canvas) => {
        const base64image = canvas.toDataURL('image/png'); // genera la imagen base64image a partir del contenido de print, el div que contiene la factura.
        image.setAttribute("href", base64image);
        const img = document.createElement("img");
        img.id = "img" + number;
        img.src = base64image;
        img.alt = "Factura: " + number;
        // No eliminamos print para que pdfDown pueda usarlo
        print.style.display = 'none'; // Solo lo ocultamos
        image.appendChild(img);
    });
}

function pdfDown(number)
{
    // Intentar obtener el elemento printable primero (div original)
    let element = document.getElementById("printable" + number);
    
    // Si el printable está oculto o no existe, usar el img generado por capture()
    if (!element || element.style.display === 'none') {
        element = document.getElementById("img" + number);
    }
    
    if (!element) {
        console.error('No se encontró ningún elemento para generar el PDF (printable' + number + ' o img' + number + ')');
        alert('No se puede generar el PDF. Por favor, recarga la página e intenta de nuevo.');
        return;
    }

    // Si es una imagen ya generada, crear PDF directamente
    if (element.tagName === 'IMG') {
        const pdf = new jsPDF({
            orientation: 'portrait',
            unit: 'mm',
            format: 'a4'
        });
        
        // Usar la imagen directamente
        const imgWidth = 190;
        const imgHeight = (element.naturalHeight * imgWidth) / element.naturalWidth;
        
        pdf.addImage(element.src, 'PNG', 10, 10, imgWidth, imgHeight);
        pdf.save('Factura_' + number + '.pdf');
        return;
    }

    // Si es el elemento HTML, usar html2canvas
    // Temporalmente mostrar el elemento si está oculto
    const wasHidden = element.style.display === 'none';
    if (wasHidden) {
        element.style.display = 'block';
    }

    html2canvas(element, {
        scale: 2,
        logging: false,
        useCORS: true,
        backgroundColor: '#ffffff'
    }).then(function(canvas) {
        // Volver a ocultar si estaba oculto
        if (wasHidden) {
            element.style.display = 'none';
        }
        
        // Convertir canvas a imagen
        const imgData = canvas.toDataURL('image/png');
        
        // Crear el PDF
        const pdf = new jsPDF({
            orientation: 'portrait',
            unit: 'mm',
            format: 'a4'
        });
        
        // Calcular dimensiones para ajustar al PDF
        const imgWidth = 190;
        const imgHeight = (canvas.height * imgWidth) / canvas.width;
        
        // Agregar la imagen al PDF
        pdf.addImage(imgData, 'PNG', 10, 10, imgWidth, imgHeight);
        
        // Descargar el PDF
        pdf.save('Factura_' + number + '.pdf');
    }).catch(function(error) {
        // Volver a ocultar si estaba oculto
        if (wasHidden) {
            element.style.display = 'none';
        }
        console.error('Error al generar PDF:', error);
        alert('Error al generar el PDF. Por favor, intenta de nuevo.');
    });
}