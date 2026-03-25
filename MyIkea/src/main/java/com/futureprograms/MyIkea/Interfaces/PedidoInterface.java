package com.futureprograms.MyIkea.Interfaces;

import com.futureprograms.MyIkea.Models.Pedido;
import com.futureprograms.MyIkea.Models.Auth.User;
import java.util.List;

/**
 * Interfaz contrato para operaciones de Pedidos
 */
public interface PedidoInterface {
    List<Pedido> getAllPedidos();
    Pedido getPedidoById(Integer id);
    Pedido savePedido(Pedido pedido);
    Pedido carrito(User user);
    List<Pedido> getPedidosCompletados(User user);
}
