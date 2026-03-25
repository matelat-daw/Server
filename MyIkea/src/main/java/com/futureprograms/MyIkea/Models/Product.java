package com.futureprograms.MyIkea.Models;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;
import org.springframework.web.multipart.MultipartFile;

import java.util.List;

@Entity
@Table(name = "productoffer")
@Data
@NoArgsConstructor
@AllArgsConstructor
public class Product {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "product_id")
    private Integer productId;

    @Column(nullable = false, length = 512, name = "product_name")
    private String productName;

    @Column(name = "product_price")
    private Float productPrice;

    @Column(length = 512, name = "product_picture")
    private String productPicture;

    @ManyToOne(optional = false, fetch = FetchType.LAZY)
    @JoinColumn(name = "id_municipio", nullable = false)
    private Municipality municipio;

    @Column(nullable = false, name = "product_stock")
    private Integer productStock;

    @ManyToMany(mappedBy = "products")
    private List<Pedido> pedidos;

    @Transient
    private MultipartFile productPictureFile;
}