package com.futureprograms.MyIkea.Controllers;

import org.springframework.security.authentication.AnonymousAuthenticationToken;
import org.springframework.security.core.Authentication;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.ControllerAdvice;
import org.springframework.web.bind.annotation.ModelAttribute;
import com.futureprograms.MyIkea.Models.Auth.User;

@ControllerAdvice
public class MainController {
    @ModelAttribute
    public void addAttributes(Model model, Authentication authentication) {
        boolean isLoged = authentication != null
                && authentication.isAuthenticated()
                && !(authentication instanceof AnonymousAuthenticationToken);
        boolean isAdmin = false;
        boolean isManager = false;
        String email = null;

        if (isLoged) {
            isAdmin = authentication.getAuthorities().stream()
                    .anyMatch(auth -> auth.getAuthority().equals("ROLE_ADMIN"));
            isManager = authentication.getAuthorities().stream()
                    .anyMatch(auth -> auth.getAuthority().equals("ROLE_MANAGER"));

            Object principal = authentication.getPrincipal();
            if (principal instanceof User logUser) {
                email = logUser.getEmail();
            }
        }

        model.addAttribute("LOGGED", isLoged);
        model.addAttribute("ADMIN", isAdmin);
        model.addAttribute("MANAGER", isManager);
        model.addAttribute("EMAIL", email);
    }
}
