package com.futureprograms.MyIkea.Interfaces;

import com.futureprograms.MyIkea.Models.Product;
import java.util.List;
import java.util.Optional;

/**
 * Interfaz contrato para operaciones de Productos
 */
public interface ProductoInterface {
    List<Product> getAllProducts();
    Optional<Product> getProductById(Integer id);
    Product saveProduct(Product product);
    void deleteProduct(Integer id);
}
