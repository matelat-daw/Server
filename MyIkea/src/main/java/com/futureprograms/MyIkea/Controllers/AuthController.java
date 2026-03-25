package com.futureprograms.MyIkea.Controllers;

import jakarta.validation.Valid;
import org.springframework.security.core.Authentication;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.validation.BindingResult;
import org.springframework.web.bind.annotation.*;
import com.futureprograms.MyIkea.Models.Auth.User;
import com.futureprograms.MyIkea.Services.auth.UserService;
import lombok.extern.slf4j.Slf4j;

import java.util.List;
import java.util.stream.Collectors;

@Controller
@Slf4j
public class AuthController {
    private final UserService userService;

    public AuthController(UserService userService) {
        this.userService = userService;
    }

    @GetMapping("/login")
    public String login() {
        return "auth/login";
    }

    @GetMapping("/register")
    public String register(Model model) {
        model.addAttribute("user", new User());
        return "auth/register";
    }

    @PostMapping("/register")
    public String registerUser(@Valid @ModelAttribute("user") User user, 
                             BindingResult result,
                             @RequestParam("confirmPassword") String confirmPassword, 
                             Model model) {
        
        if (result.hasErrors()) {
            log.warn("Errores de validación en el registro: {}", result.getAllErrors());
            return "auth/register";
        }

        if (!user.getPassword().equals(confirmPassword)) {
            model.addAttribute("passwordError", "Las contraseñas no coinciden");
            return "auth/register";
        }
        
        try {
            userService.register(user);
            log.info("Usuario registrado exitosamente: {}", user.getEmail());
            return "redirect:/login?registered=true";
        } catch (Exception e) {
            log.error("Error al registrar usuario: ", e);
            model.addAttribute("error", "Error al procesar el registro.");
            return "auth/register";
        }
    }

    @GetMapping("/users")
    public String listUsers(Model model, Authentication authentication) {
        List<User> users = userService.getAllUsers()
                .stream()
                .filter(user -> !user.getUsername().equals(authentication.getName()))
                .collect(Collectors.toList());

        model.addAttribute("users", users);
        return "auth/users";
    }

    @GetMapping("/users/delete/{id}")
    public String deleteUser(@PathVariable Integer id) {
        try {
            userService.deleteById(id);
            log.info("Usuario con ID {} eliminado", id);
            return "redirect:/users";
        } catch (Exception e) {
            log.error("Error al eliminar usuario {}: ", id, e);
            return "redirect:/users?error=No se pudo eliminar el usuario";
        }
    }
}