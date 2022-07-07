package com.accelerate.acceleronServices.reservation.service;

import com.accelerate.acceleronServices.reservation.dto.request.ReservationRequestDto;
import com.accelerate.acceleronServices.reservation.dto.response.ApiResponse;
import com.accelerate.acceleronServices.reservation.dto.response.GenericResponse;
import com.accelerate.acceleronServices.reservation.enums.StatusTextEnum;
import com.accelerate.acceleronServices.reservation.model.ReservationEntity;
import com.accelerate.acceleronServices.reservation.repository.ReservationRepository;
import com.accelerate.acceleronServices.reservation.utils.EntityDtoConversion;
import lombok.AllArgsConstructor;
import lombok.extern.slf4j.Slf4j;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.List;
import java.util.Optional;

@Service
@AllArgsConstructor
@Slf4j
public class ReservationRequestServiceImpl implements ReservationRequestService {

    @Autowired
    private ReservationRepository reservationRepository;

    @Autowired
    private EntityDtoConversion entityDtoConversion;

    @Override
    @Transactional
    public ApiResponse<GenericResponse> makeReservation(ReservationRequestDto request) {

        reservationRepository.save(entityDtoConversion.convertToEntity(request));

        return new ApiResponse<>().successResponse("Success");

    }
    @Override
    public List<ReservationEntity> getAllReservation(String search, Integer limit, Integer skip){

        if(search==null) {  //search is not provided
            if (limit == null && skip == null) {
                return reservationRepository.findAll();  //return all table rows
            }
            if (limit != null && skip == null) {
                return reservationRepository.findAllById(limit, 0);  //returns first x rows where x=limit
            }
            return reservationRepository.findAllById(limit, skip);
        }
        else{  //search is provided
            if(skip==null){
                skip=0;
            }
            if(limit==null){
                limit=10;
            }
            return reservationRepository.findByGivenString(search,limit,0);
        }
    }
    @Override
    public Optional<ReservationEntity> getReservationById(int id){
        return reservationRepository.findById(id);
    }

    @Override
    public ApiResponse<GenericResponse> deleteReservation(int id) {

        reservationRepository.deleteById(id);
        return new ApiResponse<>().successResponse("Deleted");
    }


    @Override
    public ApiResponse<GenericResponse> updateReservation(int id, ReservationRequestDto request) {

        ReservationEntity requestEntity = entityDtoConversion.convertToEntity(request);
        Optional<ReservationEntity> reservationEntity = reservationRepository.findById(id);

        if(reservationEntity.isPresent()){
            requestEntity.setId(reservationEntity.get().getId());
            reservationRepository.save(requestEntity);
            return new ApiResponse<>().successResponse("Updated your reservation");
        }
        else{ //if the id is not present
            return new ApiResponse<>().successResponse("The given id doesn't exist");
        }
    }
}
