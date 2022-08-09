package com.accelerate.acceleronServices.api;


import com.accelerate.acceleronServices.license.dto.request.LicenseRequestDto;
import com.accelerate.acceleronServices.license.dto.response.ApiResponse;
import com.accelerate.acceleronServices.license.model.LicenseEntity;
import com.accelerate.acceleronServices.license.service.LicenseRequestService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import javax.validation.Valid;
import java.util.List;

@RestController
@RequestMapping("/license")
public class LicenseApi {

    @Autowired
    LicenseRequestService licenseRequestService;

    @PostMapping
    public ResponseEntity<?> makeReservation(@RequestBody @Valid LicenseRequestDto request) {

        ApiResponse response = licenseRequestService.makeLicense(request);
        return new ResponseEntity<>(response, HttpStatus.CREATED);
    }

    @GetMapping
    public ResponseEntity<List<LicenseEntity>> getAll(@RequestParam(required = false) Integer limit, @RequestParam(required = false) Integer skip){
        return new ResponseEntity<List<LicenseEntity>>(licenseRequestService.getAll(), HttpStatus.OK);
    }

    @GetMapping("/{id}")
    public ResponseEntity<LicenseEntity> getById(@PathVariable int id){
        return new ResponseEntity<LicenseEntity>(licenseRequestService.getById(id), HttpStatus.OK);
    }

//    @GetMapping("/{license}")
//    public ResponseEntity<LicenseEntity> getByLicense(@PathVariable String license){
//        return new ResponseEntity<LicenseEntity>(licenseRequestService.getByLicense(license), HttpStatus.OK);
//    }

    @PutMapping("/{license}")
    public ResponseEntity<?> updateMachineCustomName(@PathVariable String license, @RequestParam String machineCustomName){
        ApiResponse response =  licenseRequestService.updateMachineCustomName(license, machineCustomName);
        return ResponseEntity.ok().body(response);
    }

    @PutMapping("activateLicense/{license}")
    public ResponseEntity<?> activateLicense(@PathVariable String license){
        ApiResponse response =  licenseRequestService.activateLicense(license);
        return ResponseEntity.ok().body(response);
    }

    @PutMapping("deactivateLicense/{license}")
    public ResponseEntity<?> deactivateLicense(@PathVariable String license){
        ApiResponse response =  licenseRequestService.deactivateLicense(license);
        return ResponseEntity.ok().body(response);
    }


    @DeleteMapping("/{id}")
    public ResponseEntity<ApiResponse> deleteById(@PathVariable int id){
        ApiResponse response =  licenseRequestService.deleteById(id);
        return ResponseEntity.ok().body(response);
    }

}
