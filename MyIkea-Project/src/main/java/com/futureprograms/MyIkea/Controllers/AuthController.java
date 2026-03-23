package com.futureprograms.MyIkea.Controllers;

import jakarta.validation.Valid;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.security.core.Authentication;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;
import com.futureprograms.MyIkea.Models.Auth.User;
import com.futureprograms.MyIkea.Repositories.Auth.RoleRepository;
import com.futureprograms.MyIkea.Services.auth.UserService;
import java.util.List;
import java.util.stream.Collectors;

@Controller
public class AuthController {
    @Autowired
    UserService userService;
    @Autowired
    RoleRepository roleRepository;

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
    public String registerUser(@Valid @ModelAttribute("user") User user) {
        userService.register(user);
        return "redirect:/auth/login";
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
    public String deleteUser(@PathVariable Integer id, Model model) {
        try {
            userService.deleteById(id);  // Elimina el usuario
            return "redirect:/users";
        } catch (Exception e) {
            model.addAttribute("error", "No se pudo eliminar el usuario: " + e.getMessage());
            return "redirect:/users";
        }
    }
}