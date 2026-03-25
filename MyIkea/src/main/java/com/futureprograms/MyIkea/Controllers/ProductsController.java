package com.futureprograms.MyIkea.Controllers;

import jakarta.validation.Valid;
import org.springframework.security.core.Authentication;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.multipart.MultipartFile;
import com.futureprograms.MyIkea.Models.Municipality;
import com.futureprograms.MyIkea.Models.Product;
import com.futureprograms.MyIkea.Models.Province;
import com.futureprograms.MyIkea.Services.FileUploadService;
import com.futureprograms.MyIkea.Services.LocationService;
import com.futureprograms.MyIkea.Services.ProductService;

import java.util.List;
import java.util.logging.Logger;

@Controller
@RequestMapping("/products")
public class ProductsController {
    private static final Logger LOGGER = Logger.getLogger(ProductsController.class.getName());
    private final ProductService productService;
    private final LocationService locationService;
    private final FileUploadService fileUploadService;

    public ProductsController(ProductService productService, LocationService locationService, FileUploadService fileUploadService) {
        this.productService = productService;
        this.locationService = locationService;
        this.fileUploadService = fileUploadService;
    }

    @GetMapping
    public String listProducts(Model model, Authentication authentication) {
        boolean isAdmin = authentication.getAuthorities().stream()
                .anyMatch(authority -> authority.getAuthority().equals("ROLE_ADMIN"));
        
        boolean isManagerOrAdmin = authentication.getAuthorities().stream()
                .anyMatch(authority -> authority.getAuthority().equals("ROLE_MANAGER") 
                        || authority.getAuthority().equals("ROLE_ADMIN"));

        List<Product> productos = productService.getAllProducts();
        model.addAttribute("productos", productos);
        model.addAttribute("ADMIN", isAdmin);
        model.addAttribute("CAN_CREATE", isManagerOrAdmin);

        return "products/index";
    }

    @GetMapping("/details/{id}")
    public String detailProduct(@PathVariable Integer id, Model model) {
        productService.getProductById(id).ifPresentOrElse(
                product -> model.addAttribute("producto", product),
                () -> model.addAttribute("error", "Producto no encontrado")
        );
        return "products/details";
    }

    @GetMapping("/create")
    public String createProduct(Model model, Authentication authentication) {
        // Verificar que el usuario tiene rol MANAGER o ADMIN
        boolean isManagerOrAdmin = authentication.getAuthorities().stream()
                .anyMatch(authority -> authority.getAuthority().equals("ROLE_MANAGER") 
                        || authority.getAuthority().equals("ROLE_ADMIN"));
        
        if (!isManagerOrAdmin) {
            LOGGER.warning("Intento no autorizado de acceso a crear producto por usuario: " + authentication.getName());
            return "redirect:/products";
        }

        List<Province> provincias = locationService.getAllProvincias();
        List<Municipality> municipios = locationService.getAllMunicipios();

        model.addAttribute("producto", new Product());
        model.addAttribute("provincias", provincias);
        model.addAttribute("municipios", municipios);

        return "productos/create";
    }

    @PostMapping("/create")
    public String createProduct(
            @Valid @ModelAttribute Product producto, 
            @RequestParam(value = "productPictureFile", required = false) MultipartFile file,
            Model model, 
            Authentication authentication) {
        
        System.out.println("\n========== PROCESANDO CREACIÓN DE PRODUCTO ==========");
        System.out.println("Nombre del producto: " + producto.getProductName());
        System.out.println("Archivo recibido en @RequestParam: " + (file != null ? file.getOriginalFilename() : "NULL"));
        System.out.println("Archivo en objeto producto: " + (producto.getProductPictureFile() != null ? producto.getProductPictureFile().getOriginalFilename() : "NULL"));
        System.out.flush();
        
        // Verificar que el usuario tiene rol MANAGER o ADMIN
        boolean isManagerOrAdmin = authentication.getAuthorities().stream()
                .anyMatch(authority -> authority.getAuthority().equals("ROLE_MANAGER") 
                        || authority.getAuthority().equals("ROLE_ADMIN"));
        
        if (!isManagerOrAdmin) {
            LOGGER.warning("Intento no autorizado de crear producto por usuario: " + authentication.getName());
            model.addAttribute("error", "No tienes permiso para crear productos. Solo MANAGER o ADMIN pueden hacerlo.");
            model.addAttribute("producto", producto);
            model.addAttribute("provincias", locationService.getAllProvincias());
            model.addAttribute("municipios", locationService.getAllMunicipios());
            return "products/create";
        }

        // Usar el archivo de @RequestParam primero, luego el del objeto si es necesario
        MultipartFile fileToProcess = (file != null && !file.isEmpty()) ? file : producto.getProductPictureFile();

        try {
            // Guardar imagen si existe
            if (fileToProcess != null && !fileToProcess.isEmpty()) {
                System.out.println("Tipo de archivo: " + fileToProcess.getContentType());
                System.out.println("Tamaño: " + fileToProcess.getSize() + " bytes");
                System.out.flush();
                
                String fileName = fileUploadService.saveImage(fileToProcess);
                producto.setProductPicture(fileName);
                System.out.println("✓ Imagen guardada exitosamente: " + fileName);
            } else {
                System.out.println("⚠️ No se seleccionó imagen");
                producto.setProductPicture(null);
            }
            System.out.flush();

            // Guardar producto en la base de datos
            productService.saveProduct(producto);
            LOGGER.info("Producto creado exitosamente por " + authentication.getName() + ": " + producto.getProductName());
            System.out.println("========== PRODUCTO CREADO EXITOSAMENTE ==========\n");
            System.out.flush();
            
            return "redirect:/products";
            
        } catch (IllegalArgumentException e) {
            LOGGER.warning("Validación de archivo fallida: " + e.getMessage());
            System.out.println("❌ Error: " + e.getMessage());
            System.out.flush();
            model.addAttribute("error", e.getMessage());
            model.addAttribute("producto", producto);
            model.addAttribute("provincias", locationService.getAllProvincias());
            model.addAttribute("municipios", locationService.getAllMunicipios());
            return "products/create";
            
        } catch (Exception e) {
            LOGGER.severe("Error al guardar producto: " + e.getMessage());
            System.out.println("❌ Error al guardar producto: " + e.getMessage());
            e.printStackTrace();
            System.out.flush();
            model.addAttribute("error", "Error al guardar el producto. Por favor, intente de nuevo.");
            model.addAttribute("producto", producto);
            model.addAttribute("provincias", locationService.getAllProvincias());
            model.addAttribute("municipios", locationService.getAllMunicipios());
            return "products/create";
        }
    }
}