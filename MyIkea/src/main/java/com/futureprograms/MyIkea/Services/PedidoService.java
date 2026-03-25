package com.futureprograms.MyIkea.Services;

import org.springframework.stereotype.Service;
import com.futureprograms.MyIkea.Models.Pedido;
import com.futureprograms.MyIkea.Models.Auth.User;
import com.futureprograms.MyIkea.Repositories.PedidoRepository;

import java.util.Date;
import java.util.List;

@Service
public class PedidoService {
    private final PedidoRepository pedidoRepository;

    public PedidoService(PedidoRepository pedidoRepository) {
        this.pedidoRepository = pedidoRepository;
    }

    public List<Pedido> getAllPedidos() {
        return pedidoRepository.findAll();
    }

    public Pedido getPedidoById(Integer id) {
        return pedidoRepository.findById(id).orElse(null);
    }

    public Pedido savePedido(Pedido pedido) {
        return pedidoRepository.save(pedido);
    }

    public Pedido carrito(User user) {
        return pedidoRepository.findByCompletadoFalseAndUser(user)
                .orElseGet(() -> crearCarrito(user));
    }

    private Pedido crearCarrito(User user) {
        Pedido nuevoCarrito = new Pedido();
        nuevoCarrito.setUser(user);
        nuevoCarrito.setCompletado(false);
        nuevoCarrito.setTotalPrice(0.0);
        nuevoCarrito.setFechaPedido(new Date());
        return pedidoRepository.save(nuevoCarrito);
    }

    public List<Pedido> getPedidosCompletados(User user) {
        List<Pedido> pedidos = pedidoRepository.findByCompletadoTrueAndUser(user);
        boolean updated = false;

        for (Pedido pedido : pedidos) {
            if (pedido.getFechaPedido() == null) {
                pedido.setFechaPedido(new Date());
                updated = true;
            }
        }

        if (updated) {
            pedidoRepository.saveAll(pedidos);
        }

        return pedidos;
    }
}