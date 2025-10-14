function totNumPages() // Función para la paginación
{
    return Math.ceil(window.length / window.qtty); // Calcula la cantidad de páginas que habrá, divide la cantidad de datos por 5 resultados a mostrar por página.
}

function prev(where) // Función para ir a la página anterior.
{
    if (window.page > 1) // Si la página actual es mayor que la página 1.
    {
        window.page--; // Decrementa la variable page, página anterior.
        change(window.page, window.qtty, where); // Llama a la función change pasandole el número de página a mostrar y la cantidad de datos a mostrar que siempre es 5.
    }
}

function next(where) // La Función next muestra la página siguiente.
{   
    if (window.page < totNumPages()) // Si la página en la que estoy es menor que la última.
    {
        window.page++; // Incremento la página
        change(window.page, window.qtty, where); // Llamo a la función que muestra los resultados.
    }
}

function change(page, qtty, index) // Función que muestra los resultados de a 5 en la tabla, recibe la página page, la cantidad de resultados a mostrar qtty y true si viene de index y false si viene de profile.
{
    window.page = page; // Asigno la variable page, a la variable global window.page.
    window.qtty = qtty; // Asigno la variable qtty, a la variable global window.qtty.
    if (index) // Si viene de index
    {
        var length = service.length; // Necesito el tamaño del array de servicios(Los Servicios en la Base de Datos).
    }
    else // 
    {
        var length = invoice.length; // Necesito el tamaño del array de facturas(invoice).
    }
    window.length = length; // Hago global la variable length.
    var btn_next = document.getElementById("next"); // Asigno a la variable btn_next la id del botón con id next, que muestra los resultados siguientes.
    var btn_prev = document.getElementById("prev"); // Asigno a la variable btn_prev la id del botón con id prev, que muestra los resultados anteriores.
    var page_span = document.getElementById("page"); // Asigno a la variable page_span la id del span page, que muestra el número de página.
    var table = document.getElementById("table"); // ID del div que contendrá la tabla con los datos.
    if (!index) // Si se llama desde el perfil del cliente.
    {
        var html = "<table><tr><th>Número de Factura</th><th>Servicio</th><th>Precio</th><th>Cantidad</th><th>Parcial I.V.A. Incluido</th><th>Total</th><th>Fecha</th><th>Hora</th></tr>";
        for (i = (page - 1) * qtty; i < page * qtty; i++) // Aquí hago el bucle desde la página donde esté, a la cantidad de resultados a mostrar.
        {
            if (i < length) // Si i es menor que el tamaño del array.
            {
                var my_date = date[i].split("-");
                let size = service[i].length;
                html += "<tr><td style='width: 150px;'>" + invoice[i] + "</td><td style='width: 250px;'>";
                for (k = 0; k < size; k++)
                {
                    html += service[i][k] + "<br>";
                }
                html += "</td><td style='text-align: right; width: 120px;'>";
                for (k = 0; k < size; k++)
                {
                    html += price[i][k] + "<br>";
                }
                html += "</td><td style='text-align: right;'>";
                for (k = 0; k < size; k++)
                {
                    html += qtties[i][k] + "<br>";
                }
                html += "</td><td style='text-align: right; width: 150px;'>";
                for (k = 0; k < size; k++)
                {
                    html += (parseFloat(price[i][k]) * parseInt(qtties[i][k])).toFixed(2) + " $<br><br>";
                }
                html += "</td><td>";
                html += total[i] + "</td><td>" + my_date[2] + "/" + my_date[1] + "/" + my_date[0] + "</td><td>" + time[i] + "</td></tr>"; // Cierro la tabla.
            }
        }
        html += "</table>";
        table.innerHTML = html; // La muestro en pantalla.
    }
    else // Si se llama desde index.
    {
        var html = "<table><tr><th>Servicio</th><th>Precio</th><th>Foto</th></tr>";
        for (i = (page - 1) * qtty; i < page * qtty; i++) // Aquí hago el bucle desde la página donde esté, a la cantidad de resultados a mostrar.
        {
            if (i < length) // Si i es menor que el tamaño del array.
            {
                html += "<tr><td>" + service[i] + "</td><td>" + price[i] + "</td><td><a href='#view2'><img class='mysize' src='" + img[i] + "' alt='" + service[i] + "' onclick='showImg(\"" + img[i] + "\")'></a></td></tr>";
            }
        }
        html += "</table>";
        table.innerHTML = html; // Muestro todo en pantalla.
    }

    if (length > 5) // Si la cantidad de Artículos es mayor que 5.
    {
        page_span.innerHTML = "Página: " + page; // Muestro el número de página.
        if (page == 1) // Si la página es la número 1
        {
            btn_prev.style.visibility = "hidden"; // Escondo el Botón con id prev que mostraría los resultados anteriores.
        }
        else // Si no, estoy en otra página.
        {
            btn_prev.style.visibility = "visible"; // Hago visible el botón para mostrar los resultados anteriores.
        }
        if (page == totNumPages()) // Si estoy en la última página.
        {
            btn_next.style.visibility = "hidden"; // Escondo el botón para mostrar los resultados siguientes.
        }
        else // Si no, estoy en una página intermedia o en la primera.
        {
            btn_next.style.visibility = "visible"; // Hago visible el botón para mostrar los resultados siguientes.
        }
    }
}

function toast(warn, ttl, msg) // Función para mostrar el Dialogo con los mensajes de alerta, recibe, Código, Título y Mensaje.
{
    var alerta = document.getElementById("alerta"); // La ID del botón del dialogo.
    var title = document.getElementById("title"); // Asigno a la variable title el h4 con id title.
    var message = document.getElementById("message"); // Asigno a la variable message el h5 con id message;
    if (warn == 1) // Si el código es 1, es una alerta.
    {
        title.style.backgroundColor = "#000000"; // Pongo los atributos, color de fondo negro.
        title.style.color = "yellow"; // Y color del texto amarillo.
    }
    else if (warn == 0) // Si no, si el código es 0 es un mensaje satisfactorio.
    {
        title.style.backgroundColor = "#FFFFFF"; // Pongo los atributos, color de fondo blanco.
        title.style.color = "blue"; // Y el color del texto azul.
    }
    else // Si no, viene un 2, es una alerta de error.
    {
        title.style.backgroundColor = "#000000"; // Pongo los atributos, color de fondo negro.
        title.style.color = "red"; // Y color del texto rojo.
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
            window.open("contact.php", "_blank");
        break;
        case "request":
            window.open("request.php", "_self");
        break;
        case "profile" :
            window.open("profile.php", "_self");
        break;
        case "view3" :
            window.open("index.php#view3", "_self");
        break;
        case "view2" :
            window.open("index.php#view2", "_self");
        break;
        default :
            window.open("index.php#view1", "_self");
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