package com.futureprograms.MyIkea.Controllers;

import jakarta.validation.Valid;
import org.springframework.security.core.Authentication;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;
import com.futureprograms.MyIkea.Models.Auth.User;
import com.futureprograms.MyIkea.Services.auth.UserService;

import java.util.List;
import java.util.stream.Collectors;

@Controller
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
    public String registerUser(@Valid @ModelAttribute("user") User user, @RequestParam("confirmPassword") String confirmPassword, Model model) {
        // Validar que las contraseñas coincidan
        if (!user.getPassword().equals(confirmPassword)) {
            model.addAttribute("user", user);
            return "redirect:/register?passwordMismatch=true";
        }
        
        userService.register(user);
        return "redirect:/login";
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
            return "redirect:/users";
        } catch (Exception e) {
            return "redirect:/users?error=" + e.getMessage();
        }
    }
}