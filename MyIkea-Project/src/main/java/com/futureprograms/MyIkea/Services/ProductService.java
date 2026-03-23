package com.futureprograms.MyIkea.Services;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import com.futureprograms.MyIkea.Models.Product;
import com.futureprograms.MyIkea.Repositories.ProductRepository;
import java.util.List;

@Service
public class ProductService {
    @Autowired
    private ProductRepository pr;

    public List<Product> getAllProducts() {
        return pr.findAll();
    }

    public Product getProductById(Integer id) {
        return pr.findById(id).orElse(null);
    }

    public void saveProduct(Product ofertaProducto) {
        pr.save(ofertaProducto);
    }
}
