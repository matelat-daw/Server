package com.futureprograms.MyIkea.Repositories;

import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;
import com.futureprograms.MyIkea.Models.Pedido;
import com.futureprograms.MyIkea.Models.Auth.User;

import java.util.List;
import java.util.Optional;

@Repository
public interface PedidoRepository extends JpaRepository<Pedido, Integer> {
    Optional<Pedido> findByCompletadoFalseAndUser(User user);
    List<Pedido> findByCompletadoTrueAndUser(User user);
}
