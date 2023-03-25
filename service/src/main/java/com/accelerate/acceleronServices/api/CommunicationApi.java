package com.accelerate.acceleronServices.api;

import com.accelerate.acceleronServices.communication.dto.request.CommunicationDto;

import com.accelerate.acceleronServices.communication.dto.response.ApiResponse;
import com.accelerate.acceleronServices.communication.dto.response.GenericResponse;
import com.accelerate.acceleronServices.communication.model.CommunicationEntity;
import com.accelerate.acceleronServices.communication.service.CommunicationService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import javax.validation.Valid;
import java.util.List;


@RestController
@RequestMapping("/communication")
public class CommunicationApi {

    @Autowired
    private CommunicationService communicationService;

    @PostMapping()
    public ResponseEntity<?> addCommunication(@RequestBody @Valid CommunicationDto request) {

        ApiResponse<GenericResponse> response = communicationService.addCommunication(request);
        return new ResponseEntity<>(response, HttpStatus.CREATED);
    }

    @GetMapping
    public ResponseEntity<List<CommunicationEntity>> getAll(@RequestParam(required = false) Integer limit, @RequestParam(required = false) Integer skip){
        return new ResponseEntity<List<CommunicationEntity>>(communicationService.getAllCommunication(), HttpStatus.OK);
    }

    @GetMapping("/{id}")
    public ResponseEntity<CommunicationEntity> getReservationById(@PathVariable int id){
        return new ResponseEntity<CommunicationEntity>(communicationService.getCommunicationById(id), HttpStatus.OK);
    }

    @DeleteMapping("/{id}")
    public ResponseEntity<?> deleteById(@PathVariable int id){
        ApiResponse<GenericResponse> response =  communicationService.deleteCommunicationById(id);
        return ResponseEntity.ok().body(response);

    }
}
