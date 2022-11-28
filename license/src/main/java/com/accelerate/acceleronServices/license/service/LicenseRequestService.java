package com.accelerate.acceleronServices.license.service;

import com.accelerate.acceleronServices.license.dto.request.LicenseRequestDto;
import com.accelerate.acceleronServices.license.dto.response.ApiResponse;
import com.accelerate.acceleronServices.license.model.LicenseEntity;

import java.util.List;
import java.util.Optional;

public interface LicenseRequestService {

    ApiResponse makeLicense(LicenseRequestDto request);
    List<LicenseEntity> getAll();

    LicenseEntity getById(int id);

    LicenseEntity getByLicense(String license);

    ApiResponse deleteById(int id);


    ApiResponse updateMachineCustomName(String license, String machineCustomName);

    ApiResponse activateLicense(String license);

    ApiResponse deactivateLicense(String license);


}
