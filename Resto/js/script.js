var invoices = "";
var kitchen = "";
var array = [];
var array2 = [];

let plate = document.getElementById("plate");
let platos = document.getElementById("platos");
let invoice = document.getElementById("invoice");

// Función genérica optimizada para agregar productos
function addProduct(productType) {
    const productConfig = {
        'meal': { elementId: 'meal', qtyId: 'qtty', addToKitchen: true },
        'beverage': { elementId: 'bev', qtyId: 'qtty2', addToKitchen: false },
        'dessert': { elementId: 'dess', qtyId: 'qtty3', addToKitchen: true },
        'coffee': { elementId: 'coffe', qtyId: 'qtty4', addToKitchen: true },
        'wine': { elementId: 'wine', qtyId: 'qtty5', addToKitchen: true }
    };

    const config = productConfig[productType];
    if (!config) return;

    const productElement = document.getElementById(config.elementId);
    const qtyElement = document.getElementById(config.qtyId);
    
    if (productElement && productElement.value !== "") {
        invoices += productElement.value + "," + qtyElement.value + ",";
        
        if (config.addToKitchen) {
            // Extraer el nombre del producto del value (formato: id,name,price)
            const productData = productElement.value.split(',');
            if (productData.length >= 2) {
                kitchen += productData[1] + " Cantidad: " + qtyElement.value + ", ";
            }
        }
        
        // Limpiar campos
        productElement.value = "";
        qtyElement.value = 1;
        
        // Actualizar display
        updateDisplay();
    }
}

// Funciones específicas que llaman a la función genérica (mantener compatibilidad)
function add_plate() { addProduct('meal'); }
function add_bebida() { addProduct('beverage'); }
function add_postre() { addProduct('dessert'); }
function add_coffe() { addProduct('coffee'); }
function add_wine() { addProduct('wine'); }

// Función para actualizar la visualización
function updateDisplay() {
    const plateElement = document.getElementById("plate");
    const platosElement = document.getElementById("platos");
    const invoiceElement = document.getElementById("invoice");
    
    if (plateElement) plateElement.innerHTML = invoices;
    if (platosElement) platosElement.innerHTML = kitchen;
    if (invoiceElement) invoiceElement.value = invoices;
}

function addData(data)
{
	array = data.split(';');

    switch (array[0])
    {
        case "0":
            inside = array[1].split(",");
            for (i = 0; i < inside.length; i+=4)
            {
                invoices += inside[i] + "," + inside[i + 1] + "," + inside[i + 2] + "," + inside[i + 3] + ",";
            }
            break;
        case "1":
            inside = array[1].split(",");
            for (i = 0; i < inside.length; i+=4)
            {
                invoices += inside[i] + "," + inside[i + 1] + "," + inside[i + 2] + "," + inside[i + 3] + ",";
            }
            break;
        default:
            inside = array[1].split(",");
            for (i = 0; i < inside.length; i+=4)
            {
                invoices += inside[i] + "," + inside[i + 1] + "," + inside[i + 2] + "," + inside[i + 3] + ",";
            }
    }

	if (array[0] != 1)
	{
        array2 = array[1].split(',');
		for (i = 0; i < array2.length; i+=4)
		{
			kitchen += array2[i + 1] + " Cantidad : " + array2[i + 3] + " ";
		}
		window.platos.innerHTML = kitchen;
		window.plate.innerHTML = invoices;
		window.invoice.value = invoices;
	}
    else
    {
		window.plate.innerHTML = invoices;
		window.invoice.value = invoices;
    }
}

function deleting(number)
{
	window.open("delete.php?table=" + number, "_self");
}

function verify()
{
    const pass = document.getElementById("pass");
    const pass2 = document.getElementById("pass2");
    const res = document.getElementById("res");

	if (pass.value != pass2.value)
	{
    	toast('1', 'Error en las Contraseñas:', `Las contraseñas no coinciden, por favor escríbelas nuevamente. ${pass.value} y ${pass2.value}`);
        return false;
    }
    else
    {
        if (res.checked)
        {
            if (cuit.value != "")
            {
                return true;
            }
            else
            {
                toast ("1", "Falta el Número de C.U.I.T.", "Haz Seleccionado Responsable Inscripto, Debes Introducir el Número de C.U.I.T., ¿O Tal Vez el Cliente Sea Consumidor Final?");
                return false;
            }
        }
        else
        {
            return true;
        }
    }
}

function verifyShow()
{
    let table = document.getElementById("table");
    let date = document.getElementById("date");

    if (table.value == "")
    {
        if (date.value == "")
        {
            toast(1, "Ambos Campos en Blanco", "Debes Seleccionar al Menos una Fecha o una Mesa.");
            return false;
        }
        else
        {
            return true;
        }
    }
    else
    {
        return true;
    }
}

function toast(warn, ttl, msg) {
    // Crear contenedor de toasts si no existe
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }

    // Determinar el tipo de toast según el código
    let toastClass, iconClass, bgClass;
    switch (warn) {
        case 0: // Éxito
            toastClass = 'text-bg-success';
            iconClass = 'bi-check-circle';
            bgClass = 'bg-success';
            break;
        case 1: // Advertencia
            toastClass = 'text-bg-warning';
            iconClass = 'bi-exclamation-triangle';
            bgClass = 'bg-warning';
            break;
        case 2: // Error
            toastClass = 'text-bg-danger';
            iconClass = 'bi-exclamation-triangle';
            bgClass = 'bg-danger';
            break;
        default:
            toastClass = 'text-bg-info';
            iconClass = 'bi-info-circle';
            bgClass = 'bg-info';
    }

    // Crear el toast
    const toastId = 'toast-' + Date.now();
    const toastHTML = `
        <div class="toast ${toastClass}" id="${toastId}" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="5000">
            <div class="toast-header ${bgClass} text-white">
                <i class="bi ${iconClass} me-2"></i>
                <strong class="me-auto">${ttl}</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                ${msg}
            </div>
        </div>
    `;

    // Agregar el toast al contenedor
    toastContainer.insertAdjacentHTML('beforeend', toastHTML);

    // Inicializar y mostrar el toast
    const toastElement = document.getElementById(toastId);
    const bsToast = new bootstrap.Toast(toastElement);
    bsToast.show();

    // Eliminar el toast del DOM después de que se oculte
    toastElement.addEventListener('hidden.bs.toast', function() {
        toastElement.remove();
        // Si no hay más toasts, eliminar el contenedor
        if (toastContainer.children.length === 0) {
            toastContainer.remove();
        }
    });
}

// Funciones auxiliares para toasts específicos
function showSuccess(title, message) {
    toast(0, title, message);
}

function showWarning(title, message) {
    toast(1, title, message);
}

function showError(title, message) {
    toast(2, title, message);
}

function showInfo(title, message) {
    toast(3, title, message);
}

function capture(number) // Crea una imagen de la factura del cliente, para descargarla y enviarla por E-mail, Whatsapp, etc.
{
    const print = document.getElementById("printable" + number);
    const image = document.getElementById("image" + number); // Div con ID printable0, contiene la factura.

    html2canvas(print).then((canvas) => {
        const base64image = canvas.toDataURL('image/png'); // genera la imagen base64image a partir del contenido de print, el div que contiene la factura.
        image.setAttribute("href", base64image);
        const img = document.createElement("img");
        img.id = "img" + number;
        img.src = base64image;
        img.alt = "Factura: " + number;
        print.remove();
        image.appendChild(img);
    })
}

function printIt(number) // Función que imprime la imagen en panatalla, recibe el numero de factura a imprimir.
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

function pdfDown(number)
{
    const image = document.getElementById("img" + number); // Div con ID printable0, contiene la factura.

    var doc = new jsPDF();
    doc.addImage(image, 'png', 10, 10, 240, 120, '', 'FAST');
    doc.save();
}