package com.futureprograms.MyIkea.Services.auth;

import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.stereotype.Service;
import com.futureprograms.MyIkea.Models.Auth.Role;
import com.futureprograms.MyIkea.Models.Auth.User;
import com.futureprograms.MyIkea.Repositories.Auth.RoleRepository;
import com.futureprograms.MyIkea.Repositories.Auth.UserRepository;

import java.util.Collections;
import java.util.List;

@Service
public class UserService {
    private final UserRepository userRepository;
    private final RoleRepository roleRepository;
    private final PasswordEncoder passwordEncoder;

    public UserService(UserRepository userRepository, RoleRepository roleRepository, PasswordEncoder passwordEncoder) {
        this.userRepository = userRepository;
        this.roleRepository = roleRepository;
        this.passwordEncoder = passwordEncoder;
    }

    public void register(User user) {
        user.setPassword(passwordEncoder.encode(user.getPassword()));

        Role userRole = roleRepository.findByName("USER")
                .orElseThrow(() -> new RuntimeException("El rol 'USER' no existe en la base de datos"));
        user.setRoles(Collections.singletonList(userRole));
        userRepository.save(user);
    }

    public User findByUsername(String username) {
        return userRepository.findByUsername(username)
                .orElseThrow(() -> new RuntimeException("Usuario no encontrado: " + username));
    }

    public User findById(Integer id) {
        return userRepository.findById(id)
                .orElseThrow(() -> new RuntimeException("Usuario no encontrado con ID: " + id));
    }

    public User findByEmailIfExists(String email) {
        return userRepository.findByEmail(email).orElse(null);
    }

    public void update(User user) {
        if (!userRepository.existsById(user.getId())) {
            throw new RuntimeException("Usuario no encontrado con ID: " + user.getId());
        }
        userRepository.save(user);
    }

    public void updatePassword(Integer userId, String newPassword) {
        User user = findById(userId);
        user.setPassword(passwordEncoder.encode(newPassword));
        userRepository.save(user);
    }

    public void initializeRoles() {
        if (roleRepository.findByName("USER").isEmpty()) {
            Role roleUser = new Role();
            roleUser.setName("USER");
            roleRepository.save(roleUser);
        }

        if (roleRepository.findByName("MANAGER").isEmpty()) {
            Role roleManager = new Role();
            roleManager.setName("MANAGER");
            roleRepository.save(roleManager);
        }

        if (roleRepository.findByName("ADMIN").isEmpty()) {
            Role roleAdmin = new Role();
            roleAdmin.setName("ADMIN");
            roleRepository.save(roleAdmin);
        }
    }


    public List<User> getAllUsers() {
        return userRepository.findAll();
    }

    public void deleteById(Integer id) {
        User user = userRepository.findById(id)
                .orElseThrow(() -> new RuntimeException("Usuario no encontrado con ID: " + id));

        boolean isAdmin = user.getRoles().stream()
                .anyMatch(role -> role.getName().equals("ADMIN"));

        if (isAdmin) {
            throw new RuntimeException("No se puede eliminar un usuario con el rol ADMIN.");
        }

        userRepository.deleteById(id);
    }
}