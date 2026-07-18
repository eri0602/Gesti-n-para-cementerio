package com.cementerio.sigs.controller;

import org.springframework.security.core.Authentication;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RequestParam;

@Controller
public class AuthController {

    @GetMapping("/login")
    public String login(@RequestParam(value = "error", required = false) String error,
                        @RequestParam(value = "logout", required = false) String logout,
                        Model model,
                        Authentication authentication) {
        
        // Si el usuario ya está autenticado, redirigir al dashboard
        if (authentication != null && authentication.isAuthenticated()) {
            return "redirect:/dashboard";
        }

        if (error != null) {
            model.addAttribute("error", "Usuario o contraseña incorrectos. Intente nuevamente.");
        }

        if (logout != null) {
            model.addAttribute("message", "Ha cerrado sesión correctamente.");
        }

        return "login";
    }

    @GetMapping("/")
    public String root() {
        return "redirect:/dashboard";
    }
}
