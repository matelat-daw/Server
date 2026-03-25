package com.futureprograms.MyIkea.Controllers;

import org.springframework.security.core.Authentication;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.multipart.MultipartFile;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;
import com.futureprograms.MyIkea.Models.Auth.User;
import com.futureprograms.MyIkea.Services.auth.UserService;
import com.futureprograms.MyIkea.Services.FileUploadService;

import java.io.IOException;

@Controller
@RequestMapping("/profile")
public class ProfileController {
    
    private final UserService userService;
    private final FileUploadService fileUploadService;
    
    public ProfileController(UserService userService, FileUploadService fileUploadService) {
        this.userService = userService;
        this.fileUploadService = fileUploadService;
    }
    
    /**
     * Mostrar la página de perfil del usuario autenticado
     */
    @GetMapping
    public String viewProfile(Authentication authentication, Model model) {
        if (authentication == null || !authentication.isAuthenticated()) {
            return "redirect:/login";
        }
        
        User user = userService.findByUsername(authentication.getName());
        model.addAttribute("user", user);
        return "profile/profile";
    }
    
    /**
     * Actualizar datos personales del usuario (nombre, apellido, teléfono, email)
     */
    @PostMapping("/update")
    public String updateProfile(
            @RequestParam String firstName,
            @RequestParam String lastName,
            @RequestParam String email,
            @RequestParam(required = false) String phoneNumber,
            Authentication authentication,
            RedirectAttributes redirectAttributes) {
        
        if (authentication == null || !authentication.isAuthenticated()) {
            return "redirect:/login";
        }
        
        try {
            User user = userService.findByUsername(authentication.getName());
            
            // Validar que el email no esté siendo usado por otro usuario
            User existingEmailUser = userService.findByEmailIfExists(email);
            if (existingEmailUser != null && !existingEmailUser.getId().equals(user.getId())) {
                redirectAttributes.addFlashAttribute("error", "El email ya está registrado por otro usuario");
                return "redirect:/profile";
            }
            
            // Actualizar datos
            user.setFirstName(firstName);
            user.setLastName(lastName);
            user.setEmail(email);
            user.setPhoneNumber(phoneNumber);
            
            userService.update(user);
            redirectAttributes.addFlashAttribute("success", "Perfil actualizado correctamente");
        } catch (Exception e) {
            redirectAttributes.addFlashAttribute("error", "Error al actualizar perfil: " + e.getMessage());
        }
        
        return "redirect:/profile";
    }
    
    /**
     * Cambiar la contraseña del usuario
     */
    @PostMapping("/update-password")
    public String updatePassword(
            @RequestParam String currentPassword,
            @RequestParam String newPassword,
            @RequestParam String confirmPassword,
            Authentication authentication,
            RedirectAttributes redirectAttributes) {
        
        if (authentication == null || !authentication.isAuthenticated()) {
            return "redirect:/login";
        }
        
        try {
            User user = userService.findByUsername(authentication.getName());
            
            // Validar que la contraseña actual sea correcta
            org.springframework.security.crypto.password.PasswordEncoder encoder = 
                new org.springframework.security.crypto.bcrypt.BCryptPasswordEncoder();
            
            if (!encoder.matches(currentPassword, user.getPassword())) {
                redirectAttributes.addFlashAttribute("passwordError", "La contraseña actual es incorrecta");
                return "redirect:/profile";
            }
            
            // Validar que las nuevas contraseñas coincidan
            if (!newPassword.equals(confirmPassword)) {
                redirectAttributes.addFlashAttribute("passwordError", "Las nuevas contraseñas no coinciden");
                return "redirect:/profile";
            }
            
            // Validar longitud mínima
            if (newPassword.length() < 6) {
                redirectAttributes.addFlashAttribute("passwordError", "La contraseña debe tener al menos 6 caracteres");
                return "redirect:/profile";
            }
            
            // Actualizar contraseña
            userService.updatePassword(user.getId(), newPassword);
            redirectAttributes.addFlashAttribute("passwordSuccess", "Contraseña actualizada correctamente");
        } catch (Exception e) {
            redirectAttributes.addFlashAttribute("passwordError", "Error al cambiar contraseña: " + e.getMessage());
        }
        
        return "redirect:/profile";
    }
    
    /**
     * Actualizar foto de perfil
     */
    @PostMapping("/update-picture")
    public String updateProfilePicture(
            @RequestParam("profilePicture") MultipartFile file,
            Authentication authentication,
            RedirectAttributes redirectAttributes) {
        
        if (authentication == null || !authentication.isAuthenticated()) {
            return "redirect:/login";
        }
        
        try {
            User user = userService.findByUsername(authentication.getName());
            
            // Validar que se proporcionó un archivo
            if (file.isEmpty()) {
                redirectAttributes.addFlashAttribute("pictureError", "Por favor selecciona una imagen");
                return "redirect:/profile";
            }
            
            // Eliminar imagen anterior si existe
            if (user.getProfilePicture() != null && !user.getProfilePicture().isEmpty()) {
                try {
                    fileUploadService.deleteImage(user.getProfilePicture());
                } catch (Exception e) {
                    System.out.println("Error eliminando imagen anterior: " + e.getMessage());
                    // No bloqueamos si no se puede eliminar la anterior
                }
            }
            
            // Guardar nueva imagen
            String filename = fileUploadService.saveImage(file);
            user.setProfilePicture(filename);
            userService.update(user);
            
            redirectAttributes.addFlashAttribute("pictureSuccess", "Foto de perfil actualizada correctamente");
        } catch (IOException e) {
            redirectAttributes.addFlashAttribute("pictureError", "Error al subir imagen: " + e.getMessage());
        } catch (Exception e) {
            redirectAttributes.addFlashAttribute("pictureError", "Error al actualizar foto: " + e.getMessage());
        }
        
        return "redirect:/profile";
    }
    
    /**
     * Eliminar la cuenta del usuario (requiere confirmación con contraseña)
     */
    @PostMapping("/delete")
    public String deleteAccount(
            @RequestParam String password,
            Authentication authentication,
            RedirectAttributes redirectAttributes) {
        
        if (authentication == null || !authentication.isAuthenticated()) {
            return "redirect:/login";
        }
        
        try {
            User user = userService.findByUsername(authentication.getName());
            
            // Validar contraseña
            org.springframework.security.crypto.password.PasswordEncoder encoder = 
                new org.springframework.security.crypto.bcrypt.BCryptPasswordEncoder();
            
            if (!encoder.matches(password, user.getPassword())) {
                redirectAttributes.addFlashAttribute("deleteError", "Contraseña incorrecta. No se pudo eliminar la cuenta");
                return "redirect:/profile";
            }
            
            // Eliminar foto de perfil si existe
            if (user.getProfilePicture() != null && !user.getProfilePicture().isEmpty()) {
                try {
                    fileUploadService.deleteImage(user.getProfilePicture());
                } catch (Exception e) {
                    System.out.println("Error eliminando foto de perfil: " + e.getMessage());
                }
            }
            
            // Eliminar usuario
            userService.deleteById(user.getId());
            
            // Redirigir a logout
            return "redirect:/logout";
            
        } catch (Exception e) {
            redirectAttributes.addFlashAttribute("deleteError", "Error al eliminar cuenta: " + e.getMessage());
            return "redirect:/profile";
        }
    }
}
