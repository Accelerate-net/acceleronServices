package com.accelerate.acceleronServices.reservation.repository;

import com.accelerate.acceleronServices.reservation.model.ReservationEntity;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Modifying;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

import java.util.List;

@Repository
public interface ReservationRepository extends JpaRepository<ReservationEntity, Integer> {

    //ReservationEntity findById(int requestId);

    @Modifying
    @Query(value = "insert into reservation (name, mobile_no) VALUES (?1, ?2)", nativeQuery = true)
    void makeReservation(String name, String mobileNo);

    //@Modifying
    @Query(value = "select * from reservation", nativeQuery = true)
    List<ReservationEntity> findAll();


    //@Query("FROM #{#entityName} where name = ?1")
    @Query(value = "select * from reservation where name = :name", nativeQuery = true)
    List<ReservationEntity> findByName(@Param("name") String name);

    @Modifying
    @Transactional
    @Query(value = "delete from reservation where name = :name", nativeQuery = true)
    void deleteByName(@Param("name") String name);

    @Transactional
    @Modifying
    @Query(value = "UPDATE reservation r SET r.mobile_no = ?2 WHERE r.name = ?1", nativeQuery = true)
    void updateReservation(String name, String mobileNo);
}
