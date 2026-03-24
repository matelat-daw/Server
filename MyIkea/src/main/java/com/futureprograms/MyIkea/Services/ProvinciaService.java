package com.futureprograms.MyIkea.Services;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import com.futureprograms.MyIkea.Models.Province;
import com.futureprograms.MyIkea.Repositories.ProvinceRepository;

import java.util.List;

@Service
public class ProvinciaService {
    @Autowired
    private ProvinceRepository pr;

    public List<Province> getAllProvincias() {
        return pr.findAll();
    }

    public Province getProvinciaById(Integer id) {
        return pr.findById(id).orElse(null);
    }

    public Province saveProvincia(Province provincia) {
        return pr.save(provincia);
    }

    public void deleteProvincia(Integer id) {
        pr.deleteById(id);
    }
}
