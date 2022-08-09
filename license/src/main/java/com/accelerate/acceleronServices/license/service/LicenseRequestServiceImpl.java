package com.accelerate.acceleronServices.license.service;

import com.accelerate.acceleronServices.license.dto.request.LicenseRequestDto;
import com.accelerate.acceleronServices.license.dto.response.ApiResponse;
import com.accelerate.acceleronServices.license.model.LicenseEntity;
import com.accelerate.acceleronServices.license.repository.LicenseRepository;
import com.accelerate.acceleronServices.license.utils.EntityDTOConversion;
import com.accelerate.acceleronServices.license.utils.ResponseMessage;
import lombok.AllArgsConstructor;
import lombok.extern.slf4j.Slf4j;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.stereotype.Service;

import javax.persistence.EntityNotFoundException;
import java.util.List;
import java.util.Optional;


@Service
@AllArgsConstructor
@Slf4j
public class LicenseRequestServiceImpl implements LicenseRequestService {
    @Autowired
    private LicenseRepository licenseRepository;

    @Autowired
    private EntityDTOConversion entityDTOConversion;

    @Override
    public ApiResponse makeLicense(LicenseRequestDto request) {
        LicenseEntity entity = entityDTOConversion.convertToEntity(request);
        licenseRepository.save(entity);

        ApiResponse response = new ApiResponse(true, HttpStatus.CREATED.value(), ResponseMessage.success);
        return response;

    }

    @Override
    public List<LicenseEntity> getAll() {
        return licenseRepository.findAll();
    }

    @Override
    public LicenseEntity getById(int id) {
        LicenseEntity licenseEntity = licenseRepository.getById(id);
        if (licenseEntity != null) {
            return licenseEntity;
        } else {
            throw new EntityNotFoundException(ResponseMessage.notFound);
        }
    }

    @Override
    public ApiResponse deleteById(int id) {
        if(licenseRepository.DeleteById(id) == 1){
            ApiResponse response = new ApiResponse(true, HttpStatus.OK.value(), ResponseMessage.success);
            return response;
        }
        else{
            throw new EntityNotFoundException(ResponseMessage.notFound);
        }
    }

    @Override
    public ApiResponse updateMachineCustomName(String license, String machineCustomName) {
        if(licenseRepository.updateMachineCustomName(license, machineCustomName) == 1){
            ApiResponse response = new ApiResponse(true, HttpStatus.OK.value(), ResponseMessage.success);
            return response;
        }
        else{
            throw new EntityNotFoundException(ResponseMessage.notFound);
        }
    }

    @Override
    public ApiResponse activateLicense(String license) {
        if(licenseRepository.activateLicense(license) == 1){
            ApiResponse response = new ApiResponse(true, HttpStatus.OK.value(), ResponseMessage.success);
            return response;
        }
        else{
            throw new EntityNotFoundException(ResponseMessage.notFound);
        }
    }

    @Override
    public ApiResponse deactivateLicense(String license) {
        if(licenseRepository.deactivateLicense(license) == 1){
            ApiResponse response = new ApiResponse(true, HttpStatus.OK.value(), ResponseMessage.success);
            return response;
        }
        else{
            throw new EntityNotFoundException(ResponseMessage.notFound);
        }
    }



    @Override
    public LicenseEntity getByLicense(String license) {
        LicenseEntity licenseEntity = licenseRepository.getByLicense(license);
        if (licenseEntity != null) {
            return licenseEntity;
        } else {
            throw new EntityNotFoundException(ResponseMessage.notFound);
        }
    }
}
