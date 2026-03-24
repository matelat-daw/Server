package com.futureprograms.MyIkea.Services;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import com.futureprograms.MyIkea.Models.Pedido;
import com.futureprograms.MyIkea.Models.Auth.User;
import com.futureprograms.MyIkea.Repositories.PedidoRepository;
import com.futureprograms.MyIkea.Repositories.ProductRepository;

import java.util.Date;
import java.util.List;

@Service
public class PedidoService {
    @Autowired
    private PedidoRepository pr;

    public List<Pedido> getAllPedidos(){ return pr.findAll();}

    public Pedido getPedidoById(Integer id){ return pr.findById(id).orElse(null);}

    public Pedido savePedido(Pedido pedido) { return pr.save(pedido);}

    public Pedido carrito(User user) {
        return pr.findByCompletadoFalseAndUser(user)
                .orElseGet(() -> crearCarrito(user));
    }

    private Pedido crearCarrito(User user) {
        Pedido nuevoCarrito = new Pedido();
        nuevoCarrito.setUser(user);
        nuevoCarrito.setCompletado(false);
        nuevoCarrito.setTotalPrice(0.0);
        nuevoCarrito.setFechaPedido(new Date());
        return pr.save(nuevoCarrito);
    }

    public List<Pedido> getPedidosCompletados(User user){
        List<Pedido> pedidos = pr.findByCompletadoTrueAndUser(user);
        boolean updated = false;

        for (Pedido pedido : pedidos) {
            if (pedido.getFechaPedido() == null) {
                pedido.setFechaPedido(new Date());
                updated = true;
            }
        }

        if (updated) {
            pr.saveAll(pedidos);
        }

        return pedidos;
    }
}