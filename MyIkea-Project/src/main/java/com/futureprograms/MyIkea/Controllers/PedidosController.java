package com.futureprograms.MyIkea.Controllers;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.security.core.Authentication;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.RequestMapping;
import com.futureprograms.MyIkea.Models.Pedido;
import com.futureprograms.MyIkea.Models.Product;
import com.futureprograms.MyIkea.Models.Auth.User;
import com.futureprograms.MyIkea.Services.PedidoService;
import com.futureprograms.MyIkea.Services.ProductService;
import java.util.Date;
import java.util.List;

@Controller
@RequestMapping("/")
public class PedidosController {

    @Autowired
    ProductService prs;
    @Autowired
    PedidoService pes;

    @GetMapping("/carrito")
    public String carrito(Model model, Authentication authentication) {
        User logged = (User) authentication.getPrincipal();
        Pedido carrito = pes.carrito(logged);

        if (carrito == null) {
            model.addAttribute("error", "No hay productos en el carrito.");
        } else {
            model.addAttribute("carrito", carrito);
        }

        return "carrito/carrito";
    }

    @GetMapping("/carrito/add/{id}")
    public String addCart(@PathVariable Integer id, Model model, Authentication authentication) {
        User logged = (User) authentication.getPrincipal();
        Integer id_user = logged.getId();
        Pedido carrito = pes.getAllPedidos()
                .stream()
                .filter(c -> c.getUser().getId() == id_user && !c.getCompletado())
                .findFirst()
                .orElseGet(() -> {
                    Pedido carritoNuevo = new Pedido();
                    carritoNuevo.setFechaPedido(new Date());
                    carritoNuevo.setCompletado(false);
                    carritoNuevo.setTotalPrice(0.0);
                    carritoNuevo.setUser(logged);
                    return pes.savePedido(carritoNuevo);
                });

        Product producto = prs.getProductById(id);
        if (producto == null) {
            return "redirect:/error?message=Producto+no+encontrado";
        }
        carrito.setTotalPrice(
                (carrito.getTotalPrice() == null ? 0.0 : carrito.getTotalPrice()) + producto.getProductPrice()
        );
        carrito.getProducts().add(producto);

        pes.savePedido(carrito);
        prs.saveProduct(producto);

        return "redirect:/carrito";
    }

    @GetMapping("/carrito/remove/{id}")
    public String removeFromCarrito(@PathVariable Integer id, Authentication authentication) {
        User logged = (User) authentication.getPrincipal();

        Pedido carrito = pes.carrito(logged);

        if (carrito == null) {
            return "redirect:/carrito?error=No+hay+compra+pendiente";
        }

        Product producto = prs.getProductById(id);
        if (producto == null) {
            return "redirect:/carrito?error=Producto+no+encontrado";
        }

        if (carrito.getProducts().remove(producto)) {
            carrito.setTotalPrice(carrito.getTotalPrice() - producto.getProductPrice());
            pes.savePedido(carrito);
        }

        return "redirect:/carrito";
    }

    @GetMapping("/pedidos")
    public String pedidos(Model model, Authentication authentication) {
        User logged = (User) authentication.getPrincipal();
        List<Pedido> pedidos = pes.getPedidosCompletados(logged);
        model.addAttribute("pedidos", pedidos);
        return "pedidos/pedidos";
    }

    @GetMapping("/pedidos/completar")
    public String completarPedido(Model model, Authentication authentication) {
        User logged = (User) authentication.getPrincipal();

        Pedido carrito = pes.carrito(logged);

        if (carrito == null) {
            return "redirect:/carrito?error=No+hay+carrito";
        }

        carrito.setCompletado(true);

        pes.savePedido(carrito);

        return "redirect:/pedidos";
    }

    @GetMapping("/pedidos/details/{id}")
    public String detailsPedido(@PathVariable Integer id, Model model){
        Pedido pedido = pes.getPedidoById(id);

        if (pedido == null) {
            return "redirect:/pedidos?error=Pedido+no+encontrado";
        }

        model.addAttribute("pedido", pedido);

        return "pedido/details";
    }
}