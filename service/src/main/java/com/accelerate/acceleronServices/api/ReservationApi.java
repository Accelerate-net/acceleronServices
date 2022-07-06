package com.accelerate.acceleronServices.api;



import com.accelerate.acceleronServices.reservation.dto.request.ReservationDto;
import com.accelerate.acceleronServices.reservation.dto.response.ApiResponse;
import com.accelerate.acceleronServices.reservation.dto.response.GenericResponse;
import com.accelerate.acceleronServices.reservation.enums.StatusTextEnum;
import com.accelerate.acceleronServices.reservation.model.ReservationEntity;
import com.accelerate.acceleronServices.reservation.service.ReservationRequestService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.List;
import java.util.Optional;

@RestController
@RequestMapping("/reservation")
public class ReservationApi {

    @Autowired
    private ReservationRequestService reservationRequestService;

    @PostMapping()
    public ResponseEntity<?> makeReservation(@RequestBody ReservationDto request) throws ParseException {

        //Verifying userName
        if(request.getUserName() == null || request.getUserName().length() == 0){
            return ResponseEntity.badRequest().body(new ApiResponse<>().badRequestResponse("Please provide your name"));
        }

        //Verifying mobileNo
        if(request.getMobileNo() == null||request.getMobileNo().length() < 10){
            return ResponseEntity.badRequest().body(new ApiResponse<>().badRequestResponse("Mobile number is empty or invalid"));
        }


        //verifying outlet
        if(request.getOutlet() == null || request.getOutlet().length()==0){
            return ResponseEntity.badRequest().body(new ApiResponse<>().badRequestResponse("Please select the outlet"));
        }

        //verifying date
        if(request.getDate() == null){
            return ResponseEntity.badRequest().body(new ApiResponse<>().badRequestResponse("Please provide date"));
        }

        //verifying time
        if(request.getTime() == null){
            return ResponseEntity.badRequest().body(new ApiResponse<>().badRequestResponse("Please provide time"));
        }

        //verifying count details
        try {
            if (Integer.parseInt(request.getCount()) > 30) {
                return ResponseEntity.badRequest().body(new ApiResponse<>().badRequestResponse("Maximum seats we can reserve at a time is 30. Contact us for Party Arrangements."));
            }
        }
        catch (Exception e){
            return ResponseEntity.badRequest().body(new ApiResponse<>().badRequestResponse("Please provide a valid count"));
        }

        //verifying isBirthday




        ApiResponse<GenericResponse> response = reservationRequestService.makeReservation(request);
        return ResponseEntity.ok().body(response);
    }

    @GetMapping
    public ResponseEntity<List<ReservationEntity>> getAllReservation(@RequestParam(required = false) String search,@RequestParam(required = false) Integer limit, @RequestParam(required = false) Integer skip){
        return new ResponseEntity<List<ReservationEntity>>(reservationRequestService.getAllReservation(search, limit,skip), HttpStatus.OK);
    }



    @GetMapping("/{id}")
    public ResponseEntity<Optional<ReservationEntity>> getReservationById(@PathVariable int id){
        return new ResponseEntity<Optional<ReservationEntity>>(reservationRequestService.getReservationById(id), HttpStatus.OK);
    }

    @DeleteMapping("/{id}")
    public ResponseEntity<?> deleteReservation(@PathVariable int id){
        ApiResponse<GenericResponse> response =  reservationRequestService.deleteReservation(id);
        return ResponseEntity.ok().body(response);

    }

    /*
    @PutMapping()
    public ResponseEntity<?> updateReservation(@RequestBody ReservationDto request){
        ApiResponse<GenericResponse> response = reservationService.updateReservation(request);
        return ResponseEntity.ok().body(response);

    }
     */

}
