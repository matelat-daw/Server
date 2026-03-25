package com.futureprograms.MyIkea.Models;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;
import com.futureprograms.MyIkea.Models.Auth.User;

import java.util.ArrayList;
import java.util.Date;
import java.util.List;

@Entity
@Table(name = "pedido")
@Data
@NoArgsConstructor
@AllArgsConstructor
public class Pedido {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "id_pedido")
    private Integer idPedido;

    @Column(name = "total_Price")
    private Double totalPrice;

    @Column
    @Temporal(TemporalType.TIMESTAMP)
    private Date fechaPedido;

    @Column
    private Boolean completado;

    @ManyToMany
    @JoinTable(
            name = "product_pedido",
            joinColumns = @JoinColumn(name = "id_pedido"),
            inverseJoinColumns = @JoinColumn(name = "product_id")
    )
    private List<Product> products = new ArrayList<>();

    @ManyToOne(optional = false, fetch = FetchType.LAZY)
    @JoinColumn(name = "id", referencedColumnName = "id", nullable = false)
    private User user;

    @PrePersist
    @PreUpdate
    private void ensureFechaPedido() {
        if (fechaPedido == null) {
            fechaPedido = new Date();
        }
    }
}