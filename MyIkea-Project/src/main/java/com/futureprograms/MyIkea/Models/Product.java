package com.futureprograms.MyIkea.Models;

import jakarta.persistence.*;
import org.springframework.web.multipart.MultipartFile;

import java.util.List;

@Entity
@Table(name = "productoffer")
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

    public MultipartFile getProductPictureFile() {
        return productPictureFile;
    }

    public void setProductPictureFile(MultipartFile productPictureFile) {
        this.productPictureFile = productPictureFile;
    }

    public Integer getProductId() {
        return productId;
    }

    public void setProductId(Integer productId) {
        this.productId = productId;
    }

    public String getProductName() {
        return productName;
    }

    public void setProductName(String productName) {
        this.productName = productName;
    }

    public Float getProductPrice() {
        return productPrice;
    }

    public void setProductPrice(Float productPrice) {
        this.productPrice = productPrice;
    }

    public String getProductPicture() {
        return productPicture;
    }

    public void setProductPicture(String productPicture) {
        this.productPicture = productPicture;
    }

    public Municipality getMunicipio() {
        return municipio;
    }

    public void setMunicipio(Municipality municipio) {
        this.municipio = municipio;
    }

    public Integer getProductStock() {
        return productStock;
    }

    public void setProductStock(Integer productStock) {
        this.productStock = productStock;
    }

    public List<Pedido> getPedidos() {
        return pedidos;
    }

    public void setPedidos(List<Pedido> pedidos) {
        this.pedidos = pedidos;
    }
}