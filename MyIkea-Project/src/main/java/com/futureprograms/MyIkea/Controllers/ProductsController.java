package com.futureprograms.MyIkea.Controllers;

import jakarta.validation.Valid;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.security.core.Authentication;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.multipart.MultipartFile;
import com.futureprograms.MyIkea.Models.Municipality;
import com.futureprograms.MyIkea.Models.Product;
import com.futureprograms.MyIkea.Models.Province;
import com.futureprograms.MyIkea.Services.LocationService;
import com.futureprograms.MyIkea.Services.ProductService;

import java.io.File;
import java.io.IOException;
import java.util.List;

@Controller
@RequestMapping("/productos")
public class ProductsController {
    @Autowired
    ProductService ps;
    @Autowired
    LocationService locationService;

    @GetMapping
    public String listProducts(Model model, Authentication authentication) {
        boolean isAdmin = authentication.getAuthorities().stream()
                .anyMatch(authority -> authority.getAuthority().equals("ROLE_ADMIN"));

        List<Product> productos = ps.getAllProducts();
        model.addAttribute("productos", productos);
        model.addAttribute("ADMIN", isAdmin);

        return "productos/index";
    }

    @GetMapping("/details/{id}")
    public String detailProduct(@PathVariable Integer id, Model model) {
        model.addAttribute("producto", ps.getProductById(id));
        return "productos/details";
    }

    @GetMapping("/create")
    public String createProduct(Model model) {
        List<Province> provincias = locationService.getAllProvincias();
        List<Municipality> municipios = locationService.getAllMunicipios();

        model.addAttribute("producto", new Product());
        model.addAttribute("provincias", provincias);
        model.addAttribute("municipios", municipios);

        return "productos/create";
    }

    @PostMapping("/create")
    public String createProduct(@Valid @ModelAttribute Product producto, Model model) {
        MultipartFile file = producto.getProductPictureFile();

        if (file != null && !file.isEmpty()) {
            try {
                String uploadDir = new File("src/main/resources/static/images").getAbsolutePath();

                File uploadDirFile = new File(uploadDir);
                if (!uploadDirFile.exists()) {
                    uploadDirFile.mkdirs();
                }

                String fileName = file.getOriginalFilename();
                File fileToSave = new File(uploadDir, fileName);
                file.transferTo(fileToSave);

                producto.setProductPicture(fileName);
            } catch (IOException e) {
                e.printStackTrace();
                model.addAttribute("message", "Error al Subir el Archivo.");
                return "productos/create";
            }
        } else {
            producto.setProductPicture(null);
        }

        ps.saveProduct(producto);
        return "redirect:/productos";
    }
}